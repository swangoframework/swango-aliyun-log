<?php
namespace Swango\Aliyun\Log\Action;
use Swango\Environment;

class GetLogDashboardUrl extends \BaseClient {
    protected const METHOD = 'GET', HOST = 'signin.aliyun.com', PATH = '/federation';
    private $AccessKeySecret, $AccessKeyId, $SecurityToken;
    private static $access_key_id, $access_secret, $role_arn, $region_id, $login_url_for_dashboard;
    public function prepareStsToken(): self {
        if (self::$access_key_id === null) {
            [
                'access_key_id' => self::$access_key_id,
                'access_key_secret' => self::$access_secret,
                'role_arn' => self::$role_arn,
                'region_id' => self::$region_id,
                'login_url_for_dashboard' => self::$login_url_for_dashboard
            ] = include Environment::getConfig('aliyun/log-sts');
        }

        $cache = \cache::get('AliYunSTSTokens');
        if (isset($cache)) {
            [
                $this->AccessKeySecret,
                $this->AccessKeyId,
                $this->SecurityToken
            ] = \Json::decodeAsArray($cache);
        } else {
            $iClientProfile = \Swango\Aliyun\Sts\Profile::getProfile(self::$region_id, self::$access_key_id,
                self::$access_secret);
            $client = new \Swango\Aliyun\Sts\Client($iClientProfile);
            $request = new \Swango\Aliyun\Sts\Request\AssumeRole();
            $request->setRoleArn(self::$role_arn)->setRoleSessionName('brief');
            $resp = $client->doAction($request);
            if (! property_exists($resp, 'Credentials') || ! property_exists($resp->Credentials, 'AccessKeySecret') ||
                 ! property_exists($resp->Credentials, 'AccessKeyId') ||
                 ! property_exists($resp->Credentials, 'SecurityToken') ||
                 ! is_string($resp->Credentials->AccessKeySecret) || ! is_string($resp->Credentials->AccessKeyId) ||
                 ! is_string($resp->Credentials->SecurityToken))
                throw new \ApiErrorException('AliYun STS service error');
            $this->AccessKeySecret = $resp->Credentials->AccessKeySecret;
            $this->AccessKeyId = $resp->Credentials->AccessKeyId;
            $this->SecurityToken = $resp->Credentials->SecurityToken;
            \Swlib\Archer::task('\\cache::setex',
                [
                    'AliYunSTSTokens',
                    3600 - 5,
                    \Json::encode(
                        [
                            $this->AccessKeySecret,
                            $this->AccessKeyId,
                            $this->SecurityToken
                        ])
                ]);
        }
        return $this;
    }
    public function prepareSigninToken(): self {
        $this->makeClient();
        $this->client->getUri()->withQuery(
            [
                'Action' => 'GetSigninToken',
                'AccessKeyId' => $this->AccessKeyId,
                'AccessKeySecret' => $this->AccessKeySecret,
                'SecurityToken' => $this->SecurityToken,
                'TicketType' => 'mini'
            ]);
        $this->sendHttpRequest();
        return $this;
    }
    public function getUrl(string $dashboard, array $filters = []): string {
        $resp = \Json::decodeAsObject($this->recv()->getBody());
        if (! property_exists($resp, 'SigninToken') || ! is_string($resp->SigninToken))
            throw new \ApiErrorException('AliYun Log Service get SigninToken error');

        $filters_string = '';
        if (! empty($filters)) {
            foreach ($filters as $key=>$value)
                $filters_string .= '&' . http_build_query(
                    [
                        'filters' => "$key:$value"
                    ]);
        }
        $project = \Swango\Aliyun\Log\Gateway::getDefaultProject() ?? '';
        return 'https://signin.aliyun.com/federation?' . http_build_query(
            [
                'Action' => 'Login',
                'LoginUrl' => self::$login_url_for_dashboard,
                'Destination' => "https://sls4service.console.aliyun.com/next/project/$project/dashboard/$dashboard?isShare=true&hideTopbar=true&hideSidebar=true&readOnly=true&hiddenBack=true{$filters_string}",
                'SigninToken' => $resp->SigninToken
            ]);
    }
}
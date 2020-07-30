<?php
namespace Swango\Aliyun\Log;
use Swango\Environment;

abstract class Gateway {
    private static $project;
    public static function getDefaultProject(): ?string {
        if (self::$project === null)
            self::$project = Environment::getConfig('aliyun/log')['default_logstore'];

        return self::$project;
    }
    private static function buildSearchKV(string $key, string $value) {
        if (strpos($value, ' ') === false)
            return "$key:$value";
        return $key . ':"' . $value . '"';
    }
    public static function buildSearchForAliyunLogQuery(array $where): string {
        $parts = [];
        foreach ($where as $key=>&$value)
            if (is_array($value)) {
                $count = count($value);
                if ($count === 1) {
                    $parts[] = self::buildSearchKV($key, current($value));
                } elseif ($count > 1) {
                    if (strpos($key, 'NOT ') === 0) {
                        $key = substr($key, 4);
                        $parts2 = [];
                        foreach ($value as &$v2) {
                            $parts2[] = self::buildSearchKV($key, $v2);
                        }
                        unset($v2);
                        $parts[] = 'NOT (' . implode(' OR ', $parts2) . ')';
                    } else {
                        $parts2 = [];
                        foreach ($value as &$v2) {
                            $parts2[] = self::buildSearchKV($key, $v2);
                        }
                        unset($v2);
                        $parts[] = '(' . implode(' OR ', $parts2) . ')';
                    }
                }
            } elseif ($value instanceof SearchBuild) {
                $parts[] = $value->build();
            } else {
                $parts[] = self::buildSearchKV($key, $value);
            }
        if (empty($parts))
            return '*';
        return implode(' AND ', $parts);
    }
    /**
     * 返回迭代器
     *
     * @param string|array|null $search
     * @param string|\Sql\AbstractSql $sql
     * @param int $from
     *            起始时间
     * @param int $to
     *            截止时间
     * @return \Game\User\Log\Statement 可以直接对其执行 foreach
     */
    public static function query($search, $sql, int $from, int $to, string $logstore, float $timeout = null,
        ?string $project = null): Statement {
        if (is_array($search))
            $query = self::buildSearchForAliyunLogQuery($search);
        elseif (is_null($search))
            $query = '';
        elseif (is_string($search)) {
            $query = trim($search);
            if ($query === '')
                $query = '*';
        } else
            throw new \Exception('Wrong search type: ' . gettype($search));

        if ($sql instanceof \Sql\AbstractSql)
            $query .= (isset($search) ? ' | ' : '') . $sql->getSqlString(new \Sql\Adapter\Platform\AliyunLogService());
        elseif (is_null($sql)) {
            // do nothing
        } elseif (is_string($sql)) {
            $sql = trim($sql);
            if ($sql !== '')
                $query .= (isset($search) ? ' | ' : '') . $sql;
        } else
            throw new \Exception('Wrong sql type: ' . gettype($sql));

        $request = new \Swango\Aliyun\Log\Models\Request\GetLogs($project ?? self::getDefaultProject(), $logstore,
            $from, $to, '', $query);
        $client = new \Swango\Aliyun\Log\Action\GetLogs();
        if (isset($timeout))
            $client->setTimeout($timeout);
        $client->deferExecute($request);
        return new Statement($client);
    }
}
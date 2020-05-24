<?php

namespace Iter;

class LogsRepository
{
    public $db;
    public function __construct(string $filePath)
    {
        $this->db = $filePath;
    }

    public function all()
    {
        $logsData = file_get_contents($this->db);
        if ($logsData === false) {
            throw new \Exception("Can't read file by: {$this->db}");
        } elseif ($logsData === '') {
            return [];
        }
        $result = [];
        $logsRows = explode("\n", $logsData);

        foreach ($logsRows as $log) {
            $params = explode(";", $log);
            $decodeParam = str_replace(urlencode(';'), ';', $params);
            $result[] = $decodeParam;
        }
        return $result;
    }

    public function save(array $logParams)
    {
        $encodeParam = str_replace(';', urlencode(';'), $logParams);
        $logItem = implode(';', $encodeParam);

        $log = $this->getLogsCount() == 0 ? $logItem : "\n" . $logItem;

        $result = file_put_contents($this->db, $log, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            throw new \Exception("Can't write file by: {$filePath}");
        }
    }

    public function getLogsCount()
    {
        return count($this->all());
    }

    public function getLogsByPage(int $page, $per = 10)
    {
        $offset = $page * $per - $per;
        return array_slice($this->all(), $offset, $per);
    }
}

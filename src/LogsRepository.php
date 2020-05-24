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
            $result[] = $params;
        }
        return $result;
    }

    public function save(array $logParams, $ipAddress = null)
    {
        $logs = $this->all();
        $logItem = implode(';', $logParams);
        $log = count($logs) == 0 ? $logItem : "\n" . $logItem;
        $result = file_put_contents($this->db, $log, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            throw new \Exception("Can't write file by: {$filePath}");
        }
    }
}

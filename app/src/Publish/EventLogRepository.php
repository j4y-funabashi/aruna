<?php

namespace Aruna\Publish;

class EventLogRepository
{

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addEvent($event)
    {
        $q = "REPLACE INTO event_log
                (id, type, version, data)
                VALUES (:id, :type, :version, :data)";
        $r = $this->db->prepare($q);
        $data = [
            ":id" => $event["eventID"],
            ":type" => $event["eventType"],
            ":version" => $event["eventVersion"],
            ":data" => json_encode($event["eventData"])
        ];
        $r->execute($data);
    }
}

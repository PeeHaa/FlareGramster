<?php

namespace FlareGramster\Storage\Database;

class Image
{
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function persistImage(array $data)
    {
        $query = 'INSERT INTO images';
        $query.= ' (userid, ip, uri, width, height, mime, exif)';
        $query.= ' VALUES';
        $query.= ' (:userid, :ip, :uri, :width, :height, :mime, :exif)';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute($data);

        return $this->dbConnection->lastInsertId('images_id_seq');
    }
}

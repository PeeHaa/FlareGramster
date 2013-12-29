<?php

namespace FlareGramster\Storage\Database;

use FlareGramster\Image\Data;

class Image
{
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function persistImage(Data $data)
    {
        $query = 'INSERT INTO images';
        $query.= ' (userid, ip, uri, width, height, mime, exif, image)';
        $query.= ' VALUES';
        $query.= ' (:userid, :ip, :uri, :width, :height, :mime, :exif, :image)';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'userid' => $data->getUserId(),
            'ip'     => $data->getIp(),
            'uri'    => $data->getUri(),
            'width'  => $data->getWidth(),
            'height' => $data->getHeight(),
            'mime'   => $data->getMime(),
            'exif'   => $data->getExif(),
            'image'  => $data->getHash(),
        ]);

        return $this->dbConnection->lastInsertId('images_id_seq');
    }

    public function getHash($id)
    {
        $stmt = $this->dbConnection->prepare('SELECT image FROM images WHERE id = :id');
        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->fetchColumn();
    }
}

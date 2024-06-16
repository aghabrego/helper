<?php

namespace Weirdo\Helper\Support;

use Weirdo\Helper\Support\AudioInfo;

class FileInfo extends AudioInfo
{
    /**
     * @param string $filename
     * @return void
     */
    public function __construct(string $filename)
    {
        parent::__construct($filename);
    }

    public function summary()
    {
        /** @var array $extraInfo */
        $extraInfo = $this->getFileInfo();
        /** @var string|null $fileformat */
        $fileformat = array_get($extraInfo, 'fileformat');
        /** @var string|null $fullname */
        $fullname = array_get($extraInfo, 'filename');
        
        return [
            'filesize' => array_get($extraInfo, 'filesize'),
            'filename' => array_get($extraInfo, 'filename'),
            'filenamepath' => array_get($extraInfo, 'filenamepath'),
            'fileformat' => $fileformat,
            'fullname' => "{$fullname}.$fileformat",
            'mime_type' => array_get($extraInfo, 'mime_type'),
            ...array_has($extraInfo, 'video') ? [
                'width' => array_get($extraInfo, "video.resolution_x"),
                'height' => array_get($extraInfo, "video.resolution_y"),
            ] : [],
        ];
    }
}
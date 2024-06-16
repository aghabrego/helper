<?php

namespace Weirdo\Helper\Support;

use Exception;
use JamesHeinrich\GetID3\GetID3;

class AudioInfo
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @param string $filename
     * @return void
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /** 
     * @return array|null
     */
    public function getFileInfo()
    {
        $thisFileInfo = null;
        if ($fp_remote = fopen($this->filename, 'rb')) {
            /** @var string|false */
            $localtempfilename = @tempnam('/tmp', 'getID3');
            if ($localtempfilename === false) {
                throw new  Exception("Error al crear directorio o archivo temporal en el sistema.");
            }
            if ($fp_local = fopen($localtempfilename, 'wb')) {
                while ($buffer = fread($fp_remote, 8192)) {
                    fwrite($fp_local, $buffer);
                }
                fclose($fp_local);
                // Initialize getID3 engine
                /** @var GetID3 */
                $getID3 = new GetID3;
                /** @var array */
                $thisFileInfo = $getID3->analyze($localtempfilename);
                // Delete temporary file
                unlink($localtempfilename);
            }
            fclose($fp_remote);
        }

        return $thisFileInfo;
    }
}
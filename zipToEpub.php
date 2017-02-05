<?php 



class myZipper {
	
	private $dir;
	private $zip;
	private $zipped = array();
	private $files;

	public function __construct($dir,$files) {
		$this->dir = str_replace('\\', '/', $dir);
		$this->zip = new ZipArchive();
        $this->files = $files;
	}

	public function zipFile ($file) {
        $filename = str_replace( $this->dir . '/', '', $this->dir . '/' . $file);
        
        if(!in_array($filename,$this->zipped)){
            $this->zip->addFromString($filename, file_get_contents($file));
            $this->zipped[] = $filename;
        }
	}

	public function zipDir ($dir) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file)
        {
            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, DIRECTORY_SEPARATOR)+1), array('.', '..')) )
                continue;
            
            if (!is_dir($file) === true)
            {
                $file = str_replace('\\', '/', $file);
                $filename = str_replace( $this->dir . '/', '', $this->dir . '/' . $file);
                if(!in_array($filename,$this->zipped)){
                    $this->zip->addFromString($filename, file_get_contents($file));
                    $this->zipped[] = $filename;
                }
            }
        }
	}

    public function zip($destination = '',$overwrite = false){
        if($this->zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
        foreach ($this->files as $file)
        {
            $file = str_replace('\\', '/', $this->dir . '/' . $file);
            if (is_dir($file) === true){
                $this->zipDir($file);
            } else if (is_file($file) === true)
            {
                $this->zipFile($file);
            }
        }
    }

    public function listContent($file){
        if ($this->zip->open($file) === TRUE) {
            //iterate the archive files array and display the filename or each one
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                echo $i . ': ' . $this->zip->getNameIndex($i) . '<br />';
            }
        } else {
            echo 'Failed to open the archive!';
        }
    }

    public function __destruct(){
        $this->zip->close();
        $this->zipped = array();
    }
}
/*  
zip the files, saving it as epub
 */
 
$rootPath = realpath(realpath( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'epub');

$zip = new myZipper($rootPath,array(
    'mimetype',
    'META-INF/container.xml',
    'OEBPS/content.opf',
    'OEBPS/toc.ncx',
    'OEBPS/Text/toc.xhtml',
    'OEBPS',
));
$zip->zip('One2One2015.epub');
$zip->listContent('One2One2015.epub');
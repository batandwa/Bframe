<?php
class File extends Base
{
	private $aPlainText = array('as','asp','aspx','atom','bat','cfm','cmd','hta','htm','html','js','jsp','java','mht','php','pl','py','rb','rss','sh','txt','xhtml','xml','log','out','ini','shtml','xsl','xslt','backup');
	private $aImageType = array('bm','bmp','ras','rast','fif','flo','turbot','g3','gif','ief','iefs','jfif','jfif-tbnl','jpe','jpeg','jpg','jut','nap','naplps','pic','pict','jfif','jpe','jpeg','jpg','png','x-png','tif','tiff','mcf','dwg','dxf','svf','fpx','fpx','rf','rp','wbmp','xif','xbm','ras','dwg','dxf','svf','ico','art','jps','nif','niff','pcx','pct','xpm','pnm','pbm','pgm','pgm','ppm','qif','qti','qtif','rgb','tif','tiff','bmp','xbm','xbm','pm','xpm','xwd','xwd');
	private static $types_text = array('as','asp','aspx','atom','bat','cfm','cmd','hta','htm','html','js','jsp','java','mht','php','pl','py','rb','rss','sh','txt','xhtml','xml','log','out','ini','shtml','xsl','xslt','backup');
	private static $types_archive = array("zip", "rar", "tar");
	private static $types_executable = array("php", "asp", "aspx");
	
	public function __construct()
	{
		parent::__construct();
	}
	
    public function downloadFile($file)
    {
        header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Type: application/octet-stream');
        readfile($file);
    }
    
    public static function formatSize($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function isType($type, $file_path)
    {
    	$type_array = strtolower("types_".$type);
    	if(isset(self::$$type_array))
    	{
    		return array_search(self::extension($file_path), self::$$type_array)!==false;
    	}
    	
    	return false;
    }
    
    public static function extension($file_path)
    {
    	return pathinfo($file_path, PATHINFO_EXTENSION);
    }
}
<?php

	define('QR_ECLEVEL_M', 1);


	// Supported output formats

	define('QR_FORMAT_TEXT', 0);
	define('QR_FORMAT_PNG',  1);

	class qrstr {
		public static function set(&$srctab, $x, $y, $repl, $replLen = false) {
			$srctab[$y] = substr_replace($srctab[$y], ($replLen !== false)?substr($repl,0,$replLen):$repl, $x, ($replLen !== false)?$replLen:strlen($repl));
		}
	}

//---- merged_config.php -----------------------------

/*
 * PHP QR Code encoder
 *
 * Config file, tuned-up for merged verion
 */

    define('QR_CACHEABLE', false);       // use cache - more disk reads but less CPU power, masks and format templates are stored there
    define('QR_CACHE_DIR', false);       // used when QR_CACHEABLE === true
    define('QR_LOG_DIR', false);         // default error logs dir

    define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
    define('QR_FIND_FROM_RANDOM', 2);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
    define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false

    define('QR_PNG_MAXIMUM_SIZE',  1024);                                                       // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images




//---- qrtools.php -----------------------------

    class QRtools {

        //----------------------------------------------------------------------
        public static function binarize($frame)
        {
            $len = count($frame);
            foreach ($frame as &$frameLine) {

                for($i=0; $i<$len; $i++) {
                    $frameLine[$i] = (ord($frameLine[$i])&1)?'1':'0';
                }
            }

            return $frame;
        }

        //----------------------------------------------------------------------
        public static function tcpdfBarcodeArray($code, $mode = 'QR,L', $tcPdfVersion = '4.5.037')
        {
            $barcode_array = array();

            if (!is_array($mode))
                $mode = explode(',', $mode);

            $eccLevel = 'L';

            if (count($mode) > 1) {
                $eccLevel = $mode[1];
            }

            $qrTab = QRcode::text($code, false, $eccLevel);
            $size = count($qrTab);

            $barcode_array['num_rows'] = $size;
            $barcode_array['num_cols'] = $size;
            $barcode_array['bcode'] = array();

            foreach ($qrTab as $line) {
                $arrAdd = array();
                foreach(str_split($line) as $char)
                    $arrAdd[] = ($char=='1')?1:0;
                $barcode_array['bcode'][] = $arrAdd;
            }

            return $barcode_array;
        }

        //----------------------------------------------------------------------
        public static function clearCache()
        {
            self::$frames = array();
        }

        //----------------------------------------------------------------------

        /** --------------------------------------------------------------------
         * Put an alignment marker.
         * @param frame
         * @param width
         * @param ox,oy center coordinate of the pattern
         */
        public static function putAlignmentMarker(array &$frame, $ox, $oy)
        {
            $finder = array(
                "\xa1\xa1\xa1\xa1\xa1",
                "\xa1\xa0\xa0\xa0\xa1",
                "\xa1\xa0\xa1\xa0\xa1",
                "\xa1\xa0\xa0\xa0\xa1",
                "\xa1\xa1\xa1\xa1\xa1"
            );

            $yStart = $oy-2;
            $xStart = $ox-2;

            for($y=0; $y<5; $y++) {
                QRstr::set($frame, $xStart, $yStart+$y, $finder[$y]);
            }
        }


        //----------------------------------------------------------------------
        public static function createFrame($version)
        {
            $width = self::$capacity[$version][QRCAP_WIDTH];
            $frameLine = str_repeat ("\0", $width);
            $frame = array_fill(0, $width, $frameLine);

            // Finder pattern
            self::putFinderPattern($frame, 0, 0);
            self::putFinderPattern($frame, $width - 7, 0);
            self::putFinderPattern($frame, 0, $width - 7);

            // Separator
            $yOffset = $width - 7;

            for($y=0; $y<7; $y++) {
                $frame[$y][7] = "\xc0";
                $frame[$y][$width - 8] = "\xc0";
                $frame[$yOffset][7] = "\xc0";
                $yOffset++;
            }

            $setPattern = str_repeat("\xc0", 8);

            QRstr::set($frame, 0, 7, $setPattern);
            QRstr::set($frame, $width-8, 7, $setPattern);
            QRstr::set($frame, 0, $width - 8, $setPattern);

            // Format info
            $setPattern = str_repeat("\x84", 9);
            QRstr::set($frame, 0, 8, $setPattern);
            QRstr::set($frame, $width - 8, 8, $setPattern, 8);

            $yOffset = $width - 8;

            for($y=0; $y<8; $y++,$yOffset++) {
                $frame[$y][8] = "\x84";
                $frame[$yOffset][8] = "\x84";
            }

            // Timing pattern

            for($i=1; $i<$width-15; $i++) {
                $frame[6][7+$i] = chr(0x90 | ($i & 1));
                $frame[7+$i][6] = chr(0x90 | ($i & 1));
            }

            // Alignment pattern
            self::putAlignmentPattern($version, $frame, $width);

            // Version information
            if($version >= 7) {
                $vinf = self::getVersionPattern($version);

                $v = $vinf;

                for($x=0; $x<6; $x++) {
                    for($y=0; $y<3; $y++) {
                        $frame[($width - 11)+$y][$x] = chr(0x88 | ($v & 1));
                        $v = $v >> 1;
                    }
                }

                $v = $vinf;
                for($y=0; $y<6; $y++) {
                    for($x=0; $x<3; $x++) {
                        $frame[$y][$x+($width - 11)] = chr(0x88 | ($v & 1));
                        $v = $v >> 1;
                    }
                }
            }

            // and a little bit...
            $frame[$width - 8][8] = "\x81";

            return $frame;
        }

        //----------------------------------------------------------------------
        public static function debug($frame, $binary_mode = false)
        {
            if ($binary_mode) {

                    foreach ($frame as &$frameLine) {
                        $frameLine = join('<span class="m">&nbsp;&nbsp;</span>', explode('0', $frameLine));
                        $frameLine = join('&#9608;&#9608;', explode('1', $frameLine));
                    }

                    ?>
                <style>
                    .m { background-color: white; }
                </style>
                <?php
                    echo '<pre><tt><br/ ><br/ ><br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    echo join("<br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $frame);
                    echo '</tt></pre><br/ ><br/ ><br/ ><br/ ><br/ ><br/ >';

            } else {

                foreach ($frame as &$frameLine) {
                    $frameLine = join('<span class="m">&nbsp;</span>',  explode("\xc0", $frameLine));
                    $frameLine = join('<span class="m">&#9618;</span>', explode("\xc1", $frameLine));
                    $frameLine = join('<span class="p">&nbsp;</span>',  explode("\xa0", $frameLine));
                    $frameLine = join('<span class="p">&#9618;</span>', explode("\xa1", $frameLine));
                    $frameLine = join('<span class="s">&#9671;</span>', explode("\x84", $frameLine)); //format 0
                    $frameLine = join('<span class="s">&#9670;</span>', explode("\x85", $frameLine)); //format 1
                    $frameLine = join('<span class="x">&#9762;</span>', explode("\x81", $frameLine)); //special bit
                    $frameLine = join('<span class="c">&nbsp;</span>',  explode("\x90", $frameLine)); //clock 0
                    $frameLine = join('<span class="c">&#9719;</span>', explode("\x91", $frameLine)); //clock 1
                    $frameLine = join('<span class="f">&nbsp;</span>',  explode("\x88", $frameLine)); //version
                    $frameLine = join('<span class="f">&#9618;</span>', explode("\x89", $frameLine)); //version
                    $frameLine = join('&#9830;', explode("\x01", $frameLine));
                    $frameLine = join('&#8901;', explode("\0", $frameLine));
                }

                ?>
                <style>
                    .p { background-color: yellow; }
                    .m { background-color: #00FF00; }
                    .s { background-color: #FF0000; }
                    .c { background-color: aqua; }
                    .x { background-color: pink; }
                    .f { background-color: gold; }
                </style>
                <?php
                echo "<pre><tt>";
                echo join("<br/ >", $frame);
                echo "</tt></pre>";

            }
        }

        //----------------------------------------------------------------------
        public static function serial($frame)
        {
            return gzcompress(join("\n", $frame), 9);
        }

        //----------------------------------------------------------------------
        public static function unserial($code)
        {
            return explode("\n", gzuncompress($code));
        }

        //----------------------------------------------------------------------
        public static function newFrame($version)
        {
            if($version < 1 || $version > QRSPEC_VERSION_MAX)
                return null;

            if(!isset(self::$frames[$version])) {

                $fileName = QR_CACHE_DIR.'frame_'.$version.'.dat';

                if (QR_CACHEABLE) {
                    if (file_exists($fileName)) {
                        self::$frames[$version] = self::unserial(file_get_contents($fileName));
                    } else {
                        self::$frames[$version] = self::createFrame($version);
                        file_put_contents($fileName, self::serial(self::$frames[$version]));
                    }
                } else {
                    self::$frames[$version] = self::createFrame($version);
                }
            }

            if(is_null(self::$frames[$version]))
                return null;

            return self::$frames[$version];
        }

        //----------------------------------------------------------------------
        public static function rsBlockNum($spec)     { return $spec[0] + $spec[3]; }
        public static function rsBlockNum1($spec)    { return $spec[0]; }
        public static function rsDataCodes1($spec)   { return $spec[1]; }
        public static function rsEccCodes1($spec)    { return $spec[2]; }
        public static function rsBlockNum2($spec)    { return $spec[3]; }
        public static function rsDataCodes2($spec)   { return $spec[4]; }
        public static function rsEccCodes2($spec)    { return $spec[2]; }
        public static function rsDataLength($spec)   { return ($spec[0] * $spec[1]) + ($spec[3] * $spec[4]);    }
        public static function rsEccLength($spec)    { return ($spec[0] + $spec[3]) * $spec[2]; }

    }



//---- qrimage.php -----------------------------






    define('QR_IMAGE', true);

    class QRimage {

        //----------------------------------------------------------------------
        public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4,$saveandprint=FALSE)
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame);

            if ($filename === false) {
                Header("Content-type: image/png");
                ImagePng($image);
            } else {
                if($saveandprint===TRUE){
                    ImagePng($image, $filename);
                    header("Content-type: image/png");
                    ImagePng($image);
                }else{
                    ImagePng($image, $filename);
                }
            }

            ImageDestroy($image);
        }

        //----------------------------------------------------------------------
        public static function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85)
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame);

            if ($filename === false) {
                Header("Content-type: image/jpeg");
                ImageJpeg($image, null, $q);
            } else {
                ImageJpeg($image, $filename, $q);
            }

            ImageDestroy($image);
        }

        //----------------------------------------------------------------------
        private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4)
        {
            $h = count($frame);
            $w = strlen($frame[0]);

            $imgW = $w + 2*$outerFrame;
            $imgH = $h + 2*$outerFrame;

            $base_image =ImageCreate($imgW, $imgH);

            $col[0] = ImageColorAllocate($base_image,255,255,255);
            $col[1] = ImageColorAllocate($base_image,0,0,0);

            imagefill($base_image, 0, 0, $col[0]);

            for($y=0; $y<$h; $y++) {
                for($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        ImageSetPixel($base_image,$x+$outerFrame,$y+$outerFrame,$col[1]);
                    }
                }
            }

            $target_image =ImageCreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
            ImageDestroy($base_image);

            return $target_image;
        }
    }





    define('STRUCTURE_HEADER_BITS',  20);
    define('MAX_STRUCTURED_SYMBOLS', 16);

    class QRinputItem {

        public $mode;
        public $size;
        public $data;
        public $bstream;

        public function __construct($mode, $size, $data, $bstream = null)
        {
            $setData = array_slice($data, 0, $size);

            if (count($setData) < $size) {
                $setData = array_merge($setData, array_fill(0,$size-count($setData),0));
            }

            if(!QRinput::check($mode, $size, $setData)) {
                throw new Exception('Error m:'.$mode.',s:'.$size.',d:'.join(',',$setData));
                return null;
            }

            $this->mode = $mode;
            $this->size = $size;
            $this->data = $setData;
            $this->bstream = $bstream;
        }

        //----------------------------------------------------------------------
        public function encodeModeNum($version)
        {
            try {

                $words = (int)($this->size / 3);
                $bs = new QRbitstream();

                $val = 0x1;
                $bs->appendNum(4, $val);
                $bs->appendNum(QRspec::lengthIndicator(QR_MODE_NUM, $version), $this->size);

                for($i=0; $i<$words; $i++) {
                    $val  = (ord($this->data[$i*3  ]) - ord('0')) * 100;
                    $val += (ord($this->data[$i*3+1]) - ord('0')) * 10;
                    $val += (ord($this->data[$i*3+2]) - ord('0'));
                    $bs->appendNum(10, $val);
                }

                if($this->size - $words * 3 == 1) {
                    $val = ord($this->data[$words*3]) - ord('0');
                    $bs->appendNum(4, $val);
                } else if($this->size - $words * 3 == 2) {
                    $val  = (ord($this->data[$words*3  ]) - ord('0')) * 10;
                    $val += (ord($this->data[$words*3+1]) - ord('0'));
                    $bs->appendNum(7, $val);
                }

                $this->bstream = $bs;
                return 0;

            } catch (Exception $e) {
                return -1;
            }
        }

        //----------------------------------------------------------------------
        public function encodeModeAn($version)
        {
            try {
                $words = (int)($this->size / 2);
                $bs = new QRbitstream();

                $bs->appendNum(4, 0x02);
                $bs->appendNum(QRspec::lengthIndicator(QR_MODE_AN, $version), $this->size);

                for($i=0; $i<$words; $i++) {
                    $val  = (int)QRinput::lookAnTable(ord($this->data[$i*2  ])) * 45;
                    $val += (int)QRinput::lookAnTable(ord($this->data[$i*2+1]));

                    $bs->appendNum(11, $val);
                }

                if($this->size & 1) {
                    $val = QRinput::lookAnTable(ord($this->data[$words * 2]));
                    $bs->appendNum(6, $val);
                }

                $this->bstream = $bs;
                return 0;

            } catch (Exception $e) {
                return -1;
            }
        }

        //----------------------------------------------------------------------
        public function encodeMode8($version)
        {
            try {
                $bs = new QRbitstream();

                $bs->appendNum(4, 0x4);
                $bs->appendNum(QRspec::lengthIndicator(QR_MODE_8, $version), $this->size);

                for($i=0; $i<$this->size; $i++) {
                    $bs->appendNum(8, ord($this->data[$i]));
                }

                $this->bstream = $bs;
                return 0;

            } catch (Exception $e) {
                return -1;
            }
        }

        //----------------------------------------------------------------------
        public function encodeModeKanji($version)
        {
            try {

                $bs = new QRbitrtream();

                $bs->appendNum(4, 0x8);
                $bs->appendNum(QRspec::lengthIndicator(QR_MODE_KANJI, $version), (int)($this->size / 2));

                for($i=0; $i<$this->size; $i+=2) {
                    $val = (ord($this->data[$i]) << 8) | ord($this->data[$i+1]);
                    if($val <= 0x9ffc) {
                        $val -= 0x8140;
                    } else {
                        $val -= 0xc140;
                    }

                    $h = ($val >> 8) * 0xc0;
                    $val = ($val & 0xff) + $h;

                    $bs->appendNum(13, $val);
                }

                $this->bstream = $bs;
                return 0;

            } catch (Exception $e) {
                return -1;
            }
        }

        //----------------------------------------------------------------------
        public function encodeModeStructure()
        {
            try {
                $bs =  new QRbitstream();

                $bs->appendNum(4, 0x03);
                $bs->appendNum(4, ord($this->data[1]) - 1);
                $bs->appendNum(4, ord($this->data[0]) - 1);
                $bs->appendNum(8, ord($this->data[2]));

                $this->bstream = $bs;
                return 0;

            } catch (Exception $e) {
                return -1;
            }
        }

        //----------------------------------------------------------------------
        public function estimateBitStreamSizeOfEntry($version)
        {
            $bits = 0;

            if($version == 0)
                $version = 1;

            switch($this->mode) {
                case QR_MODE_NUM:        $bits = QRinput::estimateBitsModeNum($this->size);    break;
                case QR_MODE_AN:        $bits = QRinput::estimateBitsModeAn($this->size);    break;
                case QR_MODE_8:            $bits = QRinput::estimateBitsMode8($this->size);    break;
                case QR_MODE_KANJI:        $bits = QRinput::estimateBitsModeKanji($this->size);break;
                case QR_MODE_STRUCTURE:    return STRUCTURE_HEADER_BITS;
                default:
                    return 0;
            }

            $l = QRspec::lengthIndicator($this->mode, $version);
            $m = 1 << $l;
            $num = (int)(($this->size + $m - 1) / $m);

            $bits += $num * (4 + $l);

            return $bits;
        }

        //----------------------------------------------------------------------
        public function encodeBitStream($version)
        {
            try {

                unset($this->bstream);
                $words = QRspec::maximumWords($this->mode, $version);

                if($this->size > $words) {

                    $st1 = new QRinputItem($this->mode, $words, $this->data);
                    $st2 = new QRinputItem($this->mode, $this->size - $words, array_slice($this->data, $words));

                    $st1->encodeBitStream($version);
                    $st2->encodeBitStream($version);

                    $this->bstream = new QRbitstream();
                    $this->bstream->append($st1->bstream);
                    $this->bstream->append($st2->bstream);

                    unset($st1);
                    unset($st2);

                } else {

                    $ret = 0;

                    switch($this->mode) {
                        case QR_MODE_NUM:        $ret = $this->encodeModeNum($version);    break;
                        case QR_MODE_AN:        $ret = $this->encodeModeAn($version);    break;
                        case QR_MODE_8:            $ret = $this->encodeMode8($version);    break;
                        case QR_MODE_KANJI:        $ret = $this->encodeModeKanji($version);break;
                        case QR_MODE_STRUCTURE:    $ret = $this->encodeModeStructure();    break;

                        default:
                            break;
                    }

                    if($ret < 0)
                        return -1;
                }

                return $this->bstream->size();

            } catch (Exception $e) {
                return -1;
            }
        }
    };

    //##########################################################################

    class QRinput {

        public $items;

        private $version;
        private $level;

        //----------------------------------------------------------------------
        public function __construct($version = 0, $level = QR_ECLEVEL_L)
        {
            if ($version < 0 || $version > QRSPEC_VERSION_MAX || $level > QR_ECLEVEL_H) {
                throw new Exception('Invalid version no');
                return NULL;
            }

            $this->version = $version;
            $this->level = $level;
        }

        //----------------------------------------------------------------------
        public function getVersion()
        {
            return $this->version;
        }

        //----------------------------------------------------------------------
        public function setVersion($version)
        {
            if($version < 0 || $version > QRSPEC_VERSION_MAX) {
                throw new Exception('Invalid version no');
                return -1;
            }

            $this->version = $version;

            return 0;
        }

        //----------------------------------------------------------------------
        public function getErrorCorrectionLevel()
        {
            return $this->level;
        }

        //----------------------------------------------------------------------
        public function setErrorCorrectionLevel($level)
        {
            if($level > QR_ECLEVEL_H) {
                throw new Exception('Invalid ECLEVEL');
                return -1;
            }

            $this->level = $level;

            return 0;
        }

        //----------------------------------------------------------------------
        public function appendEntry(QRinputItem $entry)
        {
            $this->items[] = $entry;
        }

        //----------------------------------------------------------------------
        public function append($mode, $size, $data)
        {
            try {
                $entry = new QRinputItem($mode, $size, $data);
                $this->items[] = $entry;
                return 0;
            } catch (Exception $e) {
                return -1;
            }
        }

        //----------------------------------------------------------------------

        public function insertStructuredAppendHeader($size, $index, $parity)
        {
            if( $size > MAX_STRUCTURED_SYMBOLS ) {
                throw new Exception('insertStructuredAppendHeader wrong size');
            }

            if( $index <= 0 || $index > MAX_STRUCTURED_SYMBOLS ) {
                throw new Exception('insertStructuredAppendHeader wrong index');
            }

            $buf = array($size, $index, $parity);

            try {
                $entry = new QRinputItem(QR_MODE_STRUCTURE, 3, buf);
                array_unshift($this->items, $entry);
                return 0;
            } catch (Exception $e) {
                return -1;
            }
        }

        //----------------------------------------------------------------------
        public function calcParity()
        {
            $parity = 0;

            foreach($this->items as $item) {
                if($item->mode != QR_MODE_STRUCTURE) {
                    for($i=$item->size-1; $i>=0; $i--) {
                        $parity ^= $item->data[$i];
                    }
                }
            }

            return $parity;
        }

        //----------------------------------------------------------------------
        public static function checkModeNum($size, $data)
        {
            for($i=0; $i<$size; $i++) {
                if((ord($data[$i]) < ord('0')) || (ord($data[$i]) > ord('9'))){
                    return false;
                }
            }

            return true;
        }

        //----------------------------------------------------------------------
        public static function estimateBitsModeNum($size)
        {
            $w = (int)$size / 3;
            $bits = $w * 10;

            switch($size - $w * 3) {
                case 1:
                    $bits += 4;
                    break;
                case 2:
                    $bits += 7;
                    break;
                default:
                    break;
            }

            return $bits;
        }

        //----------------------------------------------------------------------
        public static $anTable = array(
            -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
            36, -1, -1, -1, 37, 38, -1, -1, -1, -1, 39, 40, -1, 41, 42, 43,
             0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 44, -1, -1, -1, -1, -1,
            -1, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,
            25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1
        );

        //----------------------------------------------------------------------
        public static function lookAnTable($c)
        {
            return (($c > 127)?-1:self::$anTable[$c]);
        }

        //----------------------------------------------------------------------
        public static function checkModeAn($size, $data)
        {
            for($i=0; $i<$size; $i++) {
                if (self::lookAnTable(ord($data[$i])) == -1) {
                    return false;
                }
            }

            return true;
        }

        //----------------------------------------------------------------------
        public static function estimateBitsModeAn($size)
        {
            $w = (int)($size / 2);
            $bits = $w * 11;

            if($size & 1) {
                $bits += 6;
            }

            return $bits;
        }

        //----------------------------------------------------------------------
        public static function estimateBitsMode8($size)
        {
            return $size * 8;
        }

        //----------------------------------------------------------------------
        public function estimateBitsModeKanji($size)
        {
            return (int)(($size / 2) * 13);
        }

        //----------------------------------------------------------------------
        public static function checkModeKanji($size, $data)
        {
            if($size & 1)
                return false;

            for($i=0; $i<$size; $i+=2) {
                $val = (ord($data[$i]) << 8) | ord($data[$i+1]);
                if( $val < 0x8140
                || ($val > 0x9ffc && $val < 0xe040)
                || $val > 0xebbf) {
                    return false;
                }
            }

            return true;
        }

        /***********************************************************************
         * Validation
         **********************************************************************/

        public static function check($mode, $size, $data)
        {
            if($size <= 0)
                return false;

            switch($mode) {
                case QR_MODE_NUM:       return self::checkModeNum($size, $data);   break;
                case QR_MODE_AN:        return self::checkModeAn($size, $data);    break;
                case QR_MODE_KANJI:     return self::checkModeKanji($size, $data); break;
                case QR_MODE_8:         return true; break;
                case QR_MODE_STRUCTURE: return true; break;

                default:
                    break;
            }

            return false;
        }


        //----------------------------------------------------------------------
        public function estimateBitStreamSize($version)
        {
            $bits = 0;

            foreach($this->items as $item) {
                $bits += $item->estimateBitStreamSizeOfEntry($version);
            }

            return $bits;
        }

        //----------------------------------------------------------------------
        public function estimateVersion()
        {
            $version = 0;
            $prev = 0;
            do {
                $prev = $version;
                $bits = $this->estimateBitStreamSize($prev);
                $version = QRspec::getMinimumVersion((int)(($bits + 7) / 8), $this->level);
                if ($version < 0) {
                    return -1;
                }
            } while ($version > $prev);

            return $version;
        }

        //----------------------------------------------------------------------
        public static function lengthOfCode($mode, $version, $bits)
        {
            $payload = $bits - 4 - QRspec::lengthIndicator($mode, $version);
            switch($mode) {
                case QR_MODE_NUM:
                    $chunks = (int)($payload / 10);
                    $remain = $payload - $chunks * 10;
                    $size = $chunks * 3;
                    if($remain >= 7) {
                        $size += 2;
                    } else if($remain >= 4) {
                        $size += 1;
                    }
                    break;
                case QR_MODE_AN:
                    $chunks = (int)($payload / 11);
                    $remain = $payload - $chunks * 11;
                    $size = $chunks * 2;
                    if($remain >= 6)
                        $size++;
                    break;
                case QR_MODE_8:
                    $size = (int)($payload / 8);
                    break;
                case QR_MODE_KANJI:
                    $size = (int)(($payload / 13) * 2);
                    break;
                case QR_MODE_STRUCTURE:
                    $size = (int)($payload / 8);
                    break;
                default:
                    $size = 0;
                    break;
            }

            $maxsize = QRspec::maximumWords($mode, $version);
            if($size < 0) $size = 0;
            if($size > $maxsize) $size = $maxsize;

            return $size;
        }

        //----------------------------------------------------------------------
        public function createBitStream()
        {
            $total = 0;

            foreach($this->items as $item) {
                $bits = $item->encodeBitStream($this->version);

                if($bits < 0)
                    return -1;

                $total += $bits;
            }

            return $total;
        }

        //----------------------------------------------------------------------
        public function convertData()
        {
            $ver = $this->estimateVersion();
            if($ver > $this->getVersion()) {
                $this->setVersion($ver);
            }

            for(;;) {
                $bits = $this->createBitStream();

                if($bits < 0)
                    return -1;

                $ver = QRspec::getMinimumVersion((int)(($bits + 7) / 8), $this->level);
                if($ver < 0) {
                    throw new Exception('WRONG VERSION');
                    return -1;
                } else if($ver > $this->getVersion()) {
                    $this->setVersion($ver);
                } else {
                    break;
                }
            }

            return 0;
        }

        //----------------------------------------------------------------------
        public function appendPaddingBit(&$bstream)
        {
            $bits = $bstream->size();
            $maxwords = QRspec::getDataLength($this->version, $this->level);
            $maxbits = $maxwords * 8;

            if ($maxbits == $bits) {
                return 0;
            }

            if ($maxbits - $bits < 5) {
                return $bstream->appendNum($maxbits - $bits, 0);
            }

            $bits += 4;
            $words = (int)(($bits + 7) / 8);

            $padding = new QRbitstream();
            $ret = $padding->appendNum($words * 8 - $bits + 4, 0);

            if($ret < 0)
                return $ret;

            $padlen = $maxwords - $words;

            if($padlen > 0) {

                $padbuf = array();
                for($i=0; $i<$padlen; $i++) {
                    $padbuf[$i] = ($i&1)?0x11:0xec;
                }

                $ret = $padding->appendBytes($padlen, $padbuf);

                if($ret < 0)
                    return $ret;

            }

            $ret = $bstream->append($padding);

            return $ret;
        }

        //----------------------------------------------------------------------
        public function mergeBitStream()
        {
            if($this->convertData() < 0) {
                return null;
            }

            $bstream = new QRbitstream();

            foreach($this->items as $item) {
                $ret = $bstream->append($item->bstream);
                if($ret < 0) {
                    return null;
                }
            }

            return $bstream;
        }

        //----------------------------------------------------------------------
        public function getBitStream()
        {

            $bstream = $this->mergeBitStream();

            if($bstream == null) {
                return null;
            }

            $ret = $this->appendPaddingBit($bstream);
            if($ret < 0) {
                return null;
            }

            return $bstream;
        }

        //----------------------------------------------------------------------
        public function getByteStream()
        {
            $bstream = $this->getBitStream();
            if($bstream == null) {
                return null;
            }

            return $bstream->toByte();
        }
    }







    class QRbitstream {

        public $data = array();

        //----------------------------------------------------------------------
        public function size()
        {
            return count($this->data);
        }

        //----------------------------------------------------------------------
        public function allocate($setLength)
        {
            $this->data = array_fill(0, $setLength, 0);
            return 0;
        }

        //----------------------------------------------------------------------
        public static function newFromNum($bits, $num)
        {
            $bstream = new QRbitstream();
            $bstream->allocate($bits);

            $mask = 1 << ($bits - 1);
            for($i=0; $i<$bits; $i++) {
                if($num & $mask) {
                    $bstream->data[$i] = 1;
                } else {
                    $bstream->data[$i] = 0;
                }
                $mask = $mask >> 1;
            }

            return $bstream;
        }

        //----------------------------------------------------------------------
        public static function newFromBytes($size, $data)
        {
            $bstream = new QRbitstream();
            $bstream->allocate($size * 8);
            $p=0;

            for($i=0; $i<$size; $i++) {
                $mask = 0x80;
                for($j=0; $j<8; $j++) {
                    if($data[$i] & $mask) {
                        $bstream->data[$p] = 1;
                    } else {
                        $bstream->data[$p] = 0;
                    }
                    $p++;
                    $mask = $mask >> 1;
                }
            }

            return $bstream;
        }

        //----------------------------------------------------------------------
        public function append(QRbitstream $arg)
        {
            if (is_null($arg)) {
                return -1;
            }

            if($arg->size() == 0) {
                return 0;
            }

            if($this->size() == 0) {
                $this->data = $arg->data;
                return 0;
            }

            $this->data = array_values(array_merge($this->data, $arg->data));

            return 0;
        }

        //----------------------------------------------------------------------
        public function appendNum($bits, $num)
        {
            if ($bits == 0)
                return 0;

            $b = QRbitstream::newFromNum($bits, $num);

            if(is_null($b))
                return -1;

            $ret = $this->append($b);
            unset($b);

            return $ret;
        }

        //----------------------------------------------------------------------
        public function appendBytes($size, $data)
        {
            if ($size == 0)
                return 0;

            $b = QRbitstream::newFromBytes($size, $data);

            if(is_null($b))
                return -1;

            $ret = $this->append($b);
            unset($b);

            return $ret;
        }

        //----------------------------------------------------------------------
        public function toByte()
        {

            $size = $this->size();

            if($size == 0) {
                return array();
            }

            $data = array_fill(0, (int)(($size + 7) / 8), 0);
            $bytes = (int)($size / 8);

            $p = 0;

            for($i=0; $i<$bytes; $i++) {
                $v = 0;
                for($j=0; $j<8; $j++) {
                    $v = $v << 1;
                    $v |= $this->data[$p];
                    $p++;
                }
                $data[$i] = $v;
            }

            if($size & 7) {
                $v = 0;
                for($j=0; $j<($size & 7); $j++) {
                    $v = $v << 1;
                    $v |= $this->data[$p];
                    $p++;
                }
                $data[$bytes] = $v;
            }

            return $data;
        }

    }




    class QRsplit {

        public $dataStr = '';
        public $input;
        public $modeHint;

        //----------------------------------------------------------------------
        public function __construct($dataStr, $input, $modeHint)
        {
            $this->dataStr  = $dataStr;
            $this->input    = $input;
            $this->modeHint = $modeHint;
        }

        //----------------------------------------------------------------------
        public static function isdigitat($str, $pos)
        {
            if ($pos >= strlen($str))
                return false;

            return ((ord($str[$pos]) >= ord('0'))&&(ord($str[$pos]) <= ord('9')));
        }

        //----------------------------------------------------------------------
        public static function isalnumat($str, $pos)
        {
            if ($pos >= strlen($str))
                return false;

            return (QRinput::lookAnTable(ord($str[$pos])) >= 0);
        }

        //----------------------------------------------------------------------
        public function identifyMode($pos)
        {
            if ($pos >= strlen($this->dataStr))
                return QR_MODE_NUL;

            $c = $this->dataStr[$pos];

            if(self::isdigitat($this->dataStr, $pos)) {
                return QR_MODE_NUM;
            } else if(self::isalnumat($this->dataStr, $pos)) {
                return QR_MODE_AN;
            } else if($this->modeHint == QR_MODE_KANJI) {

                if ($pos+1 < strlen($this->dataStr))
                {
                    $d = $this->dataStr[$pos+1];
                    $word = (ord($c) << 8) | ord($d);
                    if(($word >= 0x8140 && $word <= 0x9ffc) || ($word >= 0xe040 && $word <= 0xebbf)) {
                        return QR_MODE_KANJI;
                    }
                }
            }

            return QR_MODE_8;
        }

        //----------------------------------------------------------------------
        public function eatNum()
        {
            $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());

            $p = 0;
            while(self::isdigitat($this->dataStr, $p)) {
                $p++;
            }

            $run = $p;
            $mode = $this->identifyMode($p);

            if($mode == QR_MODE_8) {
                $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln
                     + QRinput::estimateBitsMode8(1)         // + 4 + l8
                     - QRinput::estimateBitsMode8($run + 1); // - 4 - l8
                if($dif > 0) {
                    return $this->eat8();
                }
            }
            if($mode == QR_MODE_AN) {
                $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln
                     + QRinput::estimateBitsModeAn(1)        // + 4 + la
                     - QRinput::estimateBitsModeAn($run + 1);// - 4 - la
                if($dif > 0) {
                    return $this->eatAn();
                }
            }

            $ret = $this->input->append(QR_MODE_NUM, $run, str_split($this->dataStr));
            if($ret < 0)
                return -1;

            return $run;
        }

        //----------------------------------------------------------------------
        public function eatAn()
        {
            $la = QRspec::lengthIndicator(QR_MODE_AN,  $this->input->getVersion());
            $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());

            $p = 0;

            while(self::isalnumat($this->dataStr, $p)) {
                if(self::isdigitat($this->dataStr, $p)) {
                    $q = $p;
                    while(self::isdigitat($this->dataStr, $q)) {
                        $q++;
                    }

                    $dif = QRinput::estimateBitsModeAn($p) // + 4 + la
                         + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln
                         - QRinput::estimateBitsModeAn($q); // - 4 - la

                    if($dif < 0) {
                        break;
                    } else {
                        $p = $q;
                    }
                } else {
                    $p++;
                }
            }

            $run = $p;

            if(!self::isalnumat($this->dataStr, $p)) {
                $dif = QRinput::estimateBitsModeAn($run) + 4 + $la
                     + QRinput::estimateBitsMode8(1) // + 4 + l8
                      - QRinput::estimateBitsMode8($run + 1); // - 4 - l8
                if($dif > 0) {
                    return $this->eat8();
                }
            }

            $ret = $this->input->append(QR_MODE_AN, $run, str_split($this->dataStr));
            if($ret < 0)
                return -1;

            return $run;
        }

        //----------------------------------------------------------------------
        public function eatKanji()
        {
            $p = 0;

            while($this->identifyMode($p) == QR_MODE_KANJI) {
                $p += 2;
            }

            $ret = $this->input->append(QR_MODE_KANJI, $p, str_split($this->dataStr));
            if($ret < 0)
                return -1;

            return $run;
        }

        //----------------------------------------------------------------------
        public function eat8()
        {
            $la = QRspec::lengthIndicator(QR_MODE_AN, $this->input->getVersion());
            $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());

            $p = 1;
            $dataStrLen = strlen($this->dataStr);

            while($p < $dataStrLen) {

                $mode = $this->identifyMode($p);
                if($mode == QR_MODE_KANJI) {
                    break;
                }
                if($mode == QR_MODE_NUM) {
                    $q = $p;
                    while(self::isdigitat($this->dataStr, $q)) {
                        $q++;
                    }
                    $dif = QRinput::estimateBitsMode8($p) // + 4 + l8
                         + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln
                         - QRinput::estimateBitsMode8($q); // - 4 - l8
                    if($dif < 0) {
                        break;
                    } else {
                        $p = $q;
                    }
                } else if($mode == QR_MODE_AN) {
                    $q = $p;
                    while(self::isalnumat($this->dataStr, $q)) {
                        $q++;
                    }
                    $dif = QRinput::estimateBitsMode8($p)  // + 4 + l8
                         + QRinput::estimateBitsModeAn($q - $p) + 4 + $la
                         - QRinput::estimateBitsMode8($q); // - 4 - l8
                    if($dif < 0) {
                        break;
                    } else {
                        $p = $q;
                    }
                } else {
                    $p++;
                }
            }

            $run = $p;
            $ret = $this->input->append(QR_MODE_8, $run, str_split($this->dataStr));

            if($ret < 0)
                return -1;

            return $run;
        }

        //----------------------------------------------------------------------
        public function splitString()
        {
            while (strlen($this->dataStr) > 0)
            {
                if($this->dataStr == '')
                    return 0;

                $mode = $this->identifyMode(0);

                switch ($mode) {
                    case QR_MODE_NUM: $length = $this->eatNum(); break;
                    case QR_MODE_AN:  $length = $this->eatAn(); break;
                    case QR_MODE_KANJI:
                        if ($hint == QR_MODE_KANJI)
                                $length = $this->eatKanji();
                        else    $length = $this->eat8();
                        break;
                    default: $length = $this->eat8(); break;

                }

                if($length == 0) return 0;
                if($length < 0)  return -1;

                $this->dataStr = substr($this->dataStr, $length);
            }
        }

        //----------------------------------------------------------------------
        public function toUpper()
        {
            $stringLen = strlen($this->dataStr);
            $p = 0;

            while ($p<$stringLen) {
                $mode = self::identifyMode(substr($this->dataStr, $p), $this->modeHint);
                if($mode == QR_MODE_KANJI) {
                    $p += 2;
                } else {
                    if (ord($this->dataStr[$p]) >= ord('a') && ord($this->dataStr[$p]) <= ord('z')) {
                        $this->dataStr[$p] = chr(ord($this->dataStr[$p]) - 32);
                    }
                    $p++;
                }
            }

            return $this->dataStr;
        }

        //----------------------------------------------------------------------
        public static function splitStringToQRinput($string, QRinput $input, $modeHint, $casesensitive = true)
        {
            if(is_null($string) || $string == '\0' || $string == '') {
                throw new Exception('empty string!!!');
            }

            $split = new QRsplit($string, $input, $modeHint);

            if(!$casesensitive)
                $split->toUpper();

            return $split->splitString();
        }
    }





    class QRrsItem {

        public $mm;                  // Bits per symbol
        public $nn;                  // Symbols per block (= (1<<mm)-1)
        public $alpha_to = array();  // log lookup table
        public $index_of = array();  // Antilog lookup table
        public $genpoly = array();   // Generator polynomial
        public $nroots;              // Number of generator roots = number of parity symbols
        public $fcr;                 // First consecutive root, index form
        public $prim;                // Primitive element, index form
        public $iprim;               // prim-th root of 1, index form
        public $pad;                 // Padding bytes in shortened block
        public $gfpoly;

        //----------------------------------------------------------------------
        public function modnn($x)
        {
            while ($x >= $this->nn) {
                $x -= $this->nn;
                $x = ($x >> $this->mm) + ($x & $this->nn);
            }

            return $x;
        }

        //----------------------------------------------------------------------
        public static function init_rs_char($symsize, $gfpoly, $fcr, $prim, $nroots, $pad)
        {
            // Common code for intializing a Reed-Solomon control block (char or int symbols)
            // Copyright 2004 Phil Karn, KA9Q
            // May be used under the terms of the GNU Lesser General Public License (LGPL)

            $rs = null;

            // Check parameter ranges
            if($symsize < 0 || $symsize > 8)                     return $rs;
            if($fcr < 0 || $fcr >= (1<<$symsize))                return $rs;
            if($prim <= 0 || $prim >= (1<<$symsize))             return $rs;
            if($nroots < 0 || $nroots >= (1<<$symsize))          return $rs; // Can't have more roots than symbol values!
            if($pad < 0 || $pad >= ((1<<$symsize) -1 - $nroots)) return $rs; // Too much padding

            $rs = new QRrsItem();
            $rs->mm = $symsize;
            $rs->nn = (1<<$symsize)-1;
            $rs->pad = $pad;

            $rs->alpha_to = array_fill(0, $rs->nn+1, 0);
            $rs->index_of = array_fill(0, $rs->nn+1, 0);

            // PHP style macro replacement ;)
            $NN =& $rs->nn;
            $A0 =& $NN;

            // Generate Galois field lookup tables
            $rs->index_of[0] = $A0; // log(zero) = -inf
            $rs->alpha_to[$A0] = 0; // alpha**-inf = 0
            $sr = 1;

            for($i=0; $i<$rs->nn; $i++) {
                $rs->index_of[$sr] = $i;
                $rs->alpha_to[$i] = $sr;
                $sr <<= 1;
                if($sr & (1<<$symsize)) {
                    $sr ^= $gfpoly;
                }
                $sr &= $rs->nn;
            }

            if($sr != 1){
                // field generator polynomial is not primitive!
                $rs = NULL;
                return $rs;
            }

            /* Form RS code generator polynomial from its roots */
            $rs->genpoly = array_fill(0, $nroots+1, 0);

            $rs->fcr = $fcr;
            $rs->prim = $prim;
            $rs->nroots = $nroots;
            $rs->gfpoly = $gfpoly;

            /* Find prim-th root of 1, used in decoding */
            for($iprim=1;($iprim % $prim) != 0;$iprim += $rs->nn)
            ; // intentional empty-body loop!

            $rs->iprim = (int)($iprim / $prim);
            $rs->genpoly[0] = 1;

            for ($i = 0,$root=$fcr*$prim; $i < $nroots; $i++, $root += $prim) {
                $rs->genpoly[$i+1] = 1;

                // Multiply rs->genpoly[] by  @**(root + x)
                for ($j = $i; $j > 0; $j--) {
                    if ($rs->genpoly[$j] != 0) {
                        $rs->genpoly[$j] = $rs->genpoly[$j-1] ^ $rs->alpha_to[$rs->modnn($rs->index_of[$rs->genpoly[$j]] + $root)];
                    } else {
                        $rs->genpoly[$j] = $rs->genpoly[$j-1];
                    }
                }
                // rs->genpoly[0] can never be zero
                $rs->genpoly[0] = $rs->alpha_to[$rs->modnn($rs->index_of[$rs->genpoly[0]] + $root)];
            }

            // convert rs->genpoly[] to index form for quicker encoding
            for ($i = 0; $i <= $nroots; $i++)
                $rs->genpoly[$i] = $rs->index_of[$rs->genpoly[$i]];

            return $rs;
        }

        //----------------------------------------------------------------------
        public function encode_rs_char($data, &$parity)
        {
            $MM       =& $this->mm;
            $NN       =& $this->nn;
            $ALPHA_TO =& $this->alpha_to;
            $INDEX_OF =& $this->index_of;
            $GENPOLY  =& $this->genpoly;
            $NROOTS   =& $this->nroots;
            $FCR      =& $this->fcr;
            $PRIM     =& $this->prim;
            $IPRIM    =& $this->iprim;
            $PAD      =& $this->pad;
            $A0       =& $NN;

            $parity = array_fill(0, $NROOTS, 0);

            for($i=0; $i< ($NN-$NROOTS-$PAD); $i++) {

                $feedback = $INDEX_OF[$data[$i] ^ $parity[0]];
                if($feedback != $A0) {
                    // feedback term is non-zero

                    // This line is unnecessary when GENPOLY[NROOTS] is unity, as it must
                    // always be for the polynomials constructed by init_rs()
                    $feedback = $this->modnn($NN - $GENPOLY[$NROOTS] + $feedback);

                    for($j=1;$j<$NROOTS;$j++) {
                        $parity[$j] ^= $ALPHA_TO[$this->modnn($feedback + $GENPOLY[$NROOTS-$j])];
                    }
                }

                // Shift
                array_shift($parity);
                if($feedback != $A0) {
                    array_push($parity, $ALPHA_TO[$this->modnn($feedback + $GENPOLY[0])]);
                } else {
                    array_push($parity, 0);
                }
            }
        }
    }

    //##########################################################################

    class QRrs {

        public static $items = array();

        //----------------------------------------------------------------------
        public static function init_rs($symsize, $gfpoly, $fcr, $prim, $nroots, $pad)
        {
            foreach(self::$items as $rs) {
                if($rs->pad != $pad)       continue;
                if($rs->nroots != $nroots) continue;
                if($rs->mm != $symsize)    continue;
                if($rs->gfpoly != $gfpoly) continue;
                if($rs->fcr != $fcr)       continue;
                if($rs->prim != $prim)     continue;

                return $rs;
            }

            $rs = QRrsItem::init_rs_char($symsize, $gfpoly, $fcr, $prim, $nroots, $pad);
            array_unshift(self::$items, $rs);

            return $rs;
        }
    }





	define('N1', 3);
	define('N2', 3);
	define('N3', 40);
	define('N4', 10);

	class QRmask {

		public $runLength = array();

		//----------------------------------------------------------------------
		public function __construct()
        {
            $this->runLength = array_fill(0, QRSPEC_WIDTH_MAX + 1, 0);
        }

        //----------------------------------------------------------------------
        public function writeFormatInformation($width, &$frame, $mask, $level)
        {
            $blacks = 0;
            $format =  QRspec::getFormatInfo($mask, $level);

            for($i=0; $i<8; $i++) {
                if($format & 1) {
                    $blacks += 2;
                    $v = 0x85;
                } else {
                    $v = 0x84;
                }

                $frame[8][$width - 1 - $i] = chr($v);
                if($i < 6) {
                    $frame[$i][8] = chr($v);
                } else {
                    $frame[$i + 1][8] = chr($v);
                }
                $format = $format >> 1;
            }

            for($i=0; $i<7; $i++) {
                if($format & 1) {
                    $blacks += 2;
                    $v = 0x85;
                } else {
                    $v = 0x84;
                }

                $frame[$width - 7 + $i][8] = chr($v);
                if($i == 0) {
                    $frame[8][7] = chr($v);
                } else {
                    $frame[8][6 - $i] = chr($v);
                }

                $format = $format >> 1;
            }

            return $blacks;
        }

        //----------------------------------------------------------------------
        public function mask0($x, $y) { return ($x+$y)&1;                       }
        public function mask1($x, $y) { return ($y&1);                          }
        public function mask2($x, $y) { return ($x%3);                          }
        public function mask3($x, $y) { return ($x+$y)%3;                       }
        public function mask4($x, $y) { return (((int)($y/2))+((int)($x/3)))&1; }
        public function mask5($x, $y) { return (($x*$y)&1)+($x*$y)%3;           }
        public function mask6($x, $y) { return ((($x*$y)&1)+($x*$y)%3)&1;       }
        public function mask7($x, $y) { return ((($x*$y)%3)+(($x+$y)&1))&1;     }

        //----------------------------------------------------------------------
        private function generateMaskNo($maskNo, $width, $frame)
        {
            $bitMask = array_fill(0, $width, array_fill(0, $width, 0));

            for($y=0; $y<$width; $y++) {
                for($x=0; $x<$width; $x++) {
                    if(ord($frame[$y][$x]) & 0x80) {
                        $bitMask[$y][$x] = 0;
                    } else {
                        $maskFunc = call_user_func(array($this, 'mask'.$maskNo), $x, $y);
                        $bitMask[$y][$x] = ($maskFunc == 0)?1:0;
                    }

                }
            }

            return $bitMask;
        }

        //----------------------------------------------------------------------
        public static function serial($bitFrame)
        {
            $codeArr = array();

            foreach ($bitFrame as $line)
                $codeArr[] = join('', $line);

            return gzcompress(join("\n", $codeArr), 9);
        }

        //----------------------------------------------------------------------
        public static function unserial($code)
        {
            $codeArr = array();

            $codeLines = explode("\n", gzuncompress($code));
            foreach ($codeLines as $line)
                $codeArr[] = str_split($line);

            return $codeArr;
        }

        //----------------------------------------------------------------------
        public function makeMaskNo($maskNo, $width, $s, &$d, $maskGenOnly = false)
        {
            $b = 0;
            $bitMask = array();

            $fileName = QR_CACHE_DIR.'mask_'.$maskNo.DIRECTORY_SEPARATOR.'mask_'.$width.'_'.$maskNo.'.dat';

            if (QR_CACHEABLE) {
                if (file_exists($fileName)) {
                    $bitMask = self::unserial(file_get_contents($fileName));
                } else {
                    $bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);
                    if (!file_exists(QR_CACHE_DIR.'mask_'.$maskNo))
                        mkdir(QR_CACHE_DIR.'mask_'.$maskNo);
                    file_put_contents($fileName, self::serial($bitMask));
                }
            } else {
                $bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);
            }

            if ($maskGenOnly)
                return;

            $d = $s;

            for($y=0; $y<$width; $y++) {
                for($x=0; $x<$width; $x++) {
                    if($bitMask[$y][$x] == 1) {
                        $d[$y][$x] = chr(ord($s[$y][$x]) ^ (int)$bitMask[$y][$x]);
                    }
                    $b += (int)(ord($d[$y][$x]) & 1);
                }
            }

            return $b;
        }

        //----------------------------------------------------------------------
        public function makeMask($width, $frame, $maskNo, $level)
        {
            $masked = array_fill(0, $width, str_repeat("\0", $width));
            $this->makeMaskNo($maskNo, $width, $frame, $masked);
            $this->writeFormatInformation($width, $masked, $maskNo, $level);

            return $masked;
        }

        //----------------------------------------------------------------------
        public function calcN1N3($length)
        {
            $demerit = 0;

            for($i=0; $i<$length; $i++) {

                if($this->runLength[$i] >= 5) {
                    $demerit += (N1 + ($this->runLength[$i] - 5));
                }
                if($i & 1) {
                    if(($i >= 3) && ($i < ($length-2)) && ($this->runLength[$i] % 3 == 0)) {
                        $fact = (int)($this->runLength[$i] / 3);
                        if(($this->runLength[$i-2] == $fact) &&
                           ($this->runLength[$i-1] == $fact) &&
                           ($this->runLength[$i+1] == $fact) &&
                           ($this->runLength[$i+2] == $fact)) {
                            if(($this->runLength[$i-3] < 0) || ($this->runLength[$i-3] >= (4 * $fact))) {
                                $demerit += N3;
                            } else if((($i+3) >= $length) || ($this->runLength[$i+3] >= (4 * $fact))) {
                                $demerit += N3;
                            }
                        }
                    }
                }
            }
            return $demerit;
        }

        //----------------------------------------------------------------------
        public function evaluateSymbol($width, $frame)
        {
            $head = 0;
            $demerit = 0;

            for($y=0; $y<$width; $y++) {
                $head = 0;
                $this->runLength[0] = 1;

                $frameY = $frame[$y];

                if ($y>0)
                    $frameYM = $frame[$y-1];

                for($x=0; $x<$width; $x++) {
                    if(($x > 0) && ($y > 0)) {
                        $b22 = ord($frameY[$x]) & ord($frameY[$x-1]) & ord($frameYM[$x]) & ord($frameYM[$x-1]);
                        $w22 = ord($frameY[$x]) | ord($frameY[$x-1]) | ord($frameYM[$x]) | ord($frameYM[$x-1]);

                        if(($b22 | ($w22 ^ 1))&1) {
                            $demerit += N2;
                        }
                    }
                    if(($x == 0) && (ord($frameY[$x]) & 1)) {
                        $this->runLength[0] = -1;
                        $head = 1;
                        $this->runLength[$head] = 1;
                    } else if($x > 0) {
                        if((ord($frameY[$x]) ^ ord($frameY[$x-1])) & 1) {
                            $head++;
                            $this->runLength[$head] = 1;
                        } else {
                            $this->runLength[$head]++;
                        }
                    }
                }

                $demerit += $this->calcN1N3($head+1);
            }




        }
    }

<?php
namespace CoolCaptcha;

class Captcha
{
    /**
     * Width of the image
     * @var int
     */
    public $width  = 200;

    /**
     * Height of the image
     * @var int
     */
    public $height = 70;

    /**
     * Dictionary word file (empty for random text)
     * @var string
     */
    public $wordsFile = 'words/en.php';

    /**
     * Path for resource files (fonts, words, etc.)
     * __DIR__."/Resources" by default. For security reasons, is better move this
     * directory to another location outise the web server
     *
     * @var string
     */
    public $resourcesPath;

    /**
     * Min word length (for non-dictionary random text generation)
     * @var int
     */
    public $minWordLength = 5;

    /**
     * Max word length (for non-dictionary random text generation)
     * Used for dictionary words indicating the word-length
     * for font-size modification purposes
     * @var int
     */
    public $maxWordLength = 8;

    /**
     * Sessionname to store the original text
     * @var string
     */
    public $session_var = 'captcha';

    /**
     * Background color in RGB-array
     * @var int[]
     */
    public $backgroundColor = array(255, 255, 255);

    /**
     * Foreground colors in RGB-array
     * @var int[][]
     */
    public $colors = array(
        array(27,  78,  181), // blue
        array(22,  163, 35),  // green
        array(214, 36,  7),   // red
    );

    /**
     * Shadow color in RGB-array or null. For example [0, 0, 0]
     * @var int[]
     */
    public $shadowColor = null;

    /**
     * Horizontal line through the text
     * @var int
     */
    public $lineWidth = 0;

    /**
     * Font configuration
     *
     * - font: TTF file
     * - spacing: relative pixel space between character
     * - minSize: min font size
     * - maxSize: max font size
     * @var array
     */
    public $fonts = array(
        'Antykwa'  => array('spacing' => -3, 'minSize' => 27, 'maxSize' => 30, 'font' => 'AntykwaBold.ttf'),
        'Candice'  => array('spacing' =>-1.5,'minSize' => 28, 'maxSize' => 31, 'font' => 'Candice.ttf'),
        'DingDong' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 30, 'font' => 'Ding-DongDaddyO.ttf'),
        'Duality'  => array('spacing' => -2, 'minSize' => 30, 'maxSize' => 38, 'font' => 'Duality.ttf'),
        'Heineken' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 34, 'font' => 'Heineken.ttf'),
        'Jura'     => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 32, 'font' => 'Jura.ttf'),
        'StayPuft' => array('spacing' =>-1.5,'minSize' => 28, 'maxSize' => 32, 'font' => 'StayPuft.ttf'),
        'Times'    => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 34, 'font' => 'TimesNewRomanBold.ttf'),
        'VeraSans' => array('spacing' => -1, 'minSize' => 20, 'maxSize' => 28, 'font' => 'VeraSansBold.ttf')
    );

    /** Wave configuracion in X and Y axes */
    /** @var int  */
    public $Yperiod    = 12;
    /** @var int  */
    public $Yamplitude = 14;
    /** @var int  */
    public $Xperiod    = 11;
    /** @var int  */
    public $Xamplitude = 5;

    /**
     * Letter rotation clockwise
     * @var int
     */
    public $maxRotation = 8;

    /**
     * Internal image size factor (for better image quality)
     * 1: low, 2: medium, 3: high
     * @var int
     */
    public $scale = 3;

    /**
     * Blur effect for better image quality (but slower image processing).
     * Better image results with scale=3
     * @var bool
     */
    public $blur = false;

    /**
     * Debug?
     * @var bool
     */
    public $debug = false;
    
    /**
     * Image format: jpeg or png
     * @var string
     */
    public $imageFormat = 'png';

    /**
     * GD image
     * @var resource
     */
    public $im;

    public function __construct()
    {
        $this->resourcesPath = __DIR__.'/Resources';
    }

    /**
     * Generates captcha and outputs it to the browser.
     * @return string Text answer of generated captcha
     */
    public function createImage()
    {
        $ini = microtime(true);

        /** Initialization */
        $this->imageAllocate();
        
        /** Text insertion */
        $text = $this->getCaptchaText();
        $fontcfg  = $this->fonts[array_rand($this->fonts)];
        $this->writeText($text, $fontcfg);

        /** Transformations */
        if (!empty($this->lineWidth)) {
            $this->writeLine();
        }
        $this->waveImage();
        if ($this->blur && function_exists('imagefilter')) {
            imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
        }
        $this->reduceImage();

        if ($this->debug) {
            imagestring(
                $this->im,
                1,
                1,
                $this->height-8,
                "$text {$fontcfg['font']} ".round((microtime(true)-$ini)*1000)."ms",
                $this->GdFgColor
            );
        }

        /** Output */
        $this->writeImage();
        $this->cleanup();

        return $text;
    }

    /**
     * Creates the image resources
     */
    protected function imageAllocate()
    {
        // Cleanup
        if (!empty($this->im)) {
            imagedestroy($this->im);
        }

        $this->im = imagecreatetruecolor($this->width*$this->scale, $this->height*$this->scale);

        // Background color
        $this->GdBgColor = imagecolorallocate(
            $this->im,
            $this->backgroundColor[0],
            $this->backgroundColor[1],
            $this->backgroundColor[2]
        );
        imagefilledrectangle($this->im, 0, 0, $this->width*$this->scale, $this->height*$this->scale, $this->GdBgColor);

        // Foreground color
        $color           = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
        $this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

        // Shadow color
        if (!empty($this->shadowColor) && is_array($this->shadowColor) && sizeof($this->shadowColor) >= 3) {
            $this->GdShadowColor = imagecolorallocate(
                $this->im,
                $this->shadowColor[0],
                $this->shadowColor[1],
                $this->shadowColor[2]
            );
        }
    }

    /**
     * Text generation
     *
     * @return string Text
     */
    protected function getCaptchaText()
    {
        $text = $this->getDictionaryCaptchaText();
        if (!$text) {
            $text = $this->getRandomCaptchaText();
        }
        return $text;
    }

    /**
     * Random text generation
     *
     * @param int|null Text length
     * @return string Text
     */
    protected function getRandomCaptchaText($length = null)
    {
        if (empty($length)) {
            $length = rand($this->minWordLength, $this->maxWordLength);
        }

        $words  = "abcdefghijlmnopqrstvwyz";
        $vocals = "aeiou";

        $text  = "";
        $vocal = rand(0, 1);
        for ($i=0; $i<$length; $i++) {
            if ($vocal) {
                $text .= substr($vocals, mt_rand(0, 4), 1);
            } else {
                $text .= substr($words, mt_rand(0, 22), 1);
            }
            $vocal = !$vocal;
        }
        return $text;
    }

    /**
     * Random dictionary word generation
     *
     * @param boolean $extended Add extended "fake" words
     * @return string Word
     */
    public function getDictionaryCaptchaText($extended = false)
    {
        if (empty($this->wordsFile)) {
            return false;
        }

        // Full path of words file
        if (substr($this->wordsFile, 0, 1) == '/') {
            $wordsfile = $this->wordsFile;
        } else {
            $wordsfile = $this->resourcesPath.'/'.$this->wordsFile;
        }

        if (!file_exists($wordsfile)) {
            return false;
        }

        $fp     = fopen($wordsfile, "r");
        $length = strlen(fgets($fp));
        if (!$length) {
            return false;
        }
        $line   = rand(1, (filesize($wordsfile)/$length)-2);
        if (fseek($fp, $length*$line) == -1) {
            return false;
        }
        $text = trim(fgets($fp));
        fclose($fp);


        /** Change ramdom volcals */
        if ($extended) {
            $text   = preg_split('//', $text, -1, PREG_SPLIT_NO_EMPTY);
            $vocals = array('a', 'e', 'i', 'o', 'u');
            foreach ($text as $i => $char) {
                if (mt_rand(0, 1) && in_array($char, $vocals)) {
                    $text[$i] = $vocals[mt_rand(0, 4)];
                }
            }
            $text = implode('', $text);
        }

        return $text;
    }

    /**
     * Horizontal line insertion
     */
    protected function writeLine()
    {
        $x1 = $this->width*$this->scale*.15;
        $x2 = $this->textFinalX;
        $y1 = rand($this->height*$this->scale*.40, $this->height*$this->scale*.65);
        $y2 = rand($this->height*$this->scale*.40, $this->height*$this->scale*.65);
        $width = $this->lineWidth/2*$this->scale;

        for ($i = $width*-1; $i <= $width; $i++) {
            imageline($this->im, $x1, $y1+$i, $x2, $y2+$i, $this->GdFgColor);
        }
    }

    /**
     * Text insertion
     */
    protected function writeText($text, $fontcfg = array())
    {
        if (empty($fontcfg)) {
            // Select the font configuration
            $fontcfg  = $this->fonts[array_rand($this->fonts)];
        }

        // Full path of font file
        $fontfile = $this->resourcesPath.'/fonts/'.$fontcfg['font'];


        /** Increase font-size for shortest words: 9% for each glyp missing */
        $lettersMissing = $this->maxWordLength-strlen($text);
        $fontSizefactor = 1+($lettersMissing*0.09);

        // Text generation (char by char)
        $x      = 20*$this->scale;
        $y      = round(($this->height*27/40)*$this->scale);
        $length = strlen($text);
        for ($i=0; $i<$length; $i++) {
            $degree   = rand($this->maxRotation*-1, $this->maxRotation);
            $fontsize = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->scale*$fontSizefactor;
            $letter   = substr($text, $i, 1);

            if ($this->shadowColor) {
                $coords = imagettftext(
                    $this->im,
                    $fontsize,
                    $degree,
                    $x+$this->scale,
                    $y+$this->scale,
                    $this->GdShadowColor,
                    $fontfile,
                    $letter
                );
            }
            $coords = imagettftext(
                $this->im,
                $fontsize,
                $degree,
                $x,
                $y,
                $this->GdFgColor,
                $fontfile,
                $letter
            );
            $x += ($coords[2]-$x) + ($fontcfg['spacing']*$this->scale);
        }

        $this->textFinalX = $x;
    }

    /**
     * Wave filter
     */
    protected function waveImage()
    {
        // X-axis wave generation
        $xp = $this->scale*$this->Xperiod*rand(1, 3);
        $k = rand(0, 100);
        for ($i = 0; $i < ($this->width*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                $i-1, sin($k+$i/$xp) * ($this->scale*$this->Xamplitude),
                $i, 0, 1, $this->height*$this->scale);
        }

        // Y-axis wave generation
        $k = rand(0, 100);
        $yp = $this->scale*$this->Yperiod*rand(1,2);
        for ($i = 0; $i < ($this->height*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                sin($k+$i/$yp) * ($this->scale*$this->Yamplitude), $i-1,
                0, $i, $this->width*$this->scale, 1);
        }
    }

    /**
     * Reduce the image to the final size
     */
    protected function reduceImage()
    {
        $imResampled = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($imResampled, $this->im,
            0, 0, 0, 0,
            $this->width, $this->height,
            $this->width*$this->scale, $this->height*$this->scale
        );
        imagedestroy($this->im);
        $this->im = $imResampled;
    }

    /**
     * File generation
     */
    protected function writeImage()
    {
        if ($this->imageFormat == 'png' && function_exists('imagepng')) {
            header('Content-Disposition:inline;filename="captcha.png"');
            header('Content-type: image/png');
            imagepng($this->im, null, 5);
        } else {
            header('Content-Disposition:inline;filename="captcha.jpg"');
            header('Content-type: image/jpeg');
            imagejpeg($this->im, null, 80);
        }
    }

    /**
     * Cleanup
     */
    protected function cleanup()
    {
        imagedestroy($this->im);
    }
}

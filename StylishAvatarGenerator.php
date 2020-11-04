<?php

use Intervention\Image\ImageManager;

final class StylishAvatarGenerator
{
    /**
     * @var string[] Base colors
     */
    protected $defaultColors = [
        '#5f91c7',
        '#f071a4',
        '#d8c4b6',
        '#6c9a8c',
        '#e1dfa9',
        '#acdeb4',
        '#905c4d',
        '#d3667c',
        '#2658f2',
        '#22dff2',
        '#ff1d56',
        '#ac283f',
        '#4b5362',
        '#9dd68a',
        '#f9b135'
    ];

    /**
     * @var string[] Supported styles
     */
    protected $supportedStyles = [
        'flamenko'
    ];

    /**
     * @var string Path to styles
     */
    protected $elemetsPath = 'images/';

    /**
     * Image Manager
     * @var Intervention\Image\ImageManager
     */
    private $manager;

    const MAX_SIZE = 600;

    public function __construct($useImagickDriver = false)
    {
        $this->manager = new ImageManager($useImagickDriver ? ['driver' => 'imagick'] : ['driver' => 'gd']);
    }

    private function maxVariants(string $elemDir){
        return count(scandir($elemDir)) - 2;
    }

    private function generateRandomSeed($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Generate number by seed
     * @param string $seed
     * @param int $minNum minimal number
     * @param null|int $maxNum maxumal number
     * @return int
     */
    private function numBySeed(string $seed, int $minNum, int $maxNum)
    {
        $number = crc32($seed) % 10;
        return $number < $minNum ? $minNum : ($number > $maxNum ? $maxNum : $number);
    }

    /**
     * Check style for supported
     * @param string $style
     * @return bool
     * @throws Exception
     */
    private function checkStyle(string $style)
    {
        if(!in_array($style, $this->supportedStyles))
            throw new Exception('Style not supported', 422);
        return true;
    }

    /**
     * @param mixed $seed Seed string. Id null - generate random
     * @param string $style
     * @param int $size
     * @return \Intervention\Image\Image
     * @throws Exception
     */
    public function generateFace($seed, string $style, int $size = 600) : \Intervention\Image\Image
    {
        $seed = $seed ?? $this->generateRandomSeed();

        $size = $size > self::MAX_SIZE ? self::MAX_SIZE : $size;
        $this->checkStyle($style);

        $backgroundColor = $this->defaultColors[$this->numBySeed($seed, 0, count($this->defaultColors))];

        $basePath = $this->elemetsPath . $style . '/';
        $eye = $basePath . 'eye/eye' . $this->numBySeed($seed, 1, $this->maxVariants($basePath . 'eye')) . '.png';
        $eyebrows = $basePath . 'eyebrows/eyebrows' . $this->numBySeed(md5($seed), 1, $this->maxVariants($basePath . 'eyebrows')) . '.png';
        $nose = $basePath . 'nose/nose' . $this->numBySeed($seed, 1, $this->maxVariants($basePath . 'nose')) . '.png';
        $mouth = $basePath . 'mouth/mouth' . $this->numBySeed(base64_encode($seed), 1, $this->maxVariants($basePath . 'mouth')) . '.png';

        $image = $this->manager
            ->canvas($size, $size, $backgroundColor);

        $image->insert($mouth, 'center-bottom', 10, 20 + mt_rand(0,20));
        $image->insert($nose, 'center-center');
        $image->insert($eye, 'center-top', 0, 120);
        $image->insert($eyebrows, 'center-top', 0, 20);

        return $image;
    }
}
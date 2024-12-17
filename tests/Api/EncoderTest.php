<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Encoders\MediaTypeEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\MediaType;
use Intervention\Image\Origin;
use Mockery;
use PHPUnit\Framework\TestCase;

class EncoderTest extends TestCase
{
    private Encoder $encoder;
    private ImageInterface $jpg;
    private ImageInterface $png;
    private ImageInterface $gif;
    private ImageInterface $tif;
    private ImageInterface $webp;
    private ImageInterface $webpx;
    private ImageInterface $avif;
    private ImageInterface $heic;

    public function setUp(): void
    {
        $manager = ImageManager::gd();
        $this->jpg = $manager->read(
            $manager->create(100, 100)->encode(new MediaTypeEncoder('image/jpeg'))->toFilePointer()
        );
        $this->png = $manager->read(
            $manager->create(100, 100)->encode(new MediaTypeEncoder('image/png'))->toFilePointer()
        );
        $this->gif = $manager->read(
            $manager->create(100, 100)->encode(new MediaTypeEncoder('image/gif'))->toFilePointer()
        );

        if (function_exists('imagecreatefromwebp')) {
            $this->webp = $manager->read(
                $manager->create(100, 100)->encode(new MediaTypeEncoder('image/webp'))->toFilePointer()
            );
            $this->webpx = $manager->read(
                $manager->create(100, 100)->encode(new MediaTypeEncoder('image/x-webp'))->toFilePointer()
            );
        }

        if (function_exists('imagecreatefromavif')) {
            $this->avif = $manager->read(
                $manager->create(100, 100)->encode(new MediaTypeEncoder('image/avif'))->toFilePointer()
            );
        }

        $this->encoder = new Encoder();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance(): void
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         */
        $this->assertInstanceOf(Encoder::class, $this->encoder);
    }

    public function testRun(): void
    {
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->jpg)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->png)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->gif)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->jpg)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->png)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->gif)));
        $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->jpg)));
        $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->png)));
        $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->gif)));
        $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->jpg)));
        $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->png)));
        $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->gif)));

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->webp)));
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->webp)));
            $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->webp)));
            $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->webp)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->jpg)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->png)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->gif)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->webp)));

            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->webpx)));
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->webpx)));
            $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->webpx)));
            $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->webpx)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->webpx)));
        }
        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->avif)));
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->avif)));
            $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->avif)));
            $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->avif)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->jpg)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->png)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->gif)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->avif)));
        }

        if (function_exists('imagecreatefromwebp') && function_exists('imagecreatefromavif')) {
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->avif)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->webp)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->webpx)));
        }
    }

    public function testGetFormat(): void
    {
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => 'jpg'])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('png', $this->encoder->setParams(['fm' => 'png'])->getFormat($this->getImageByMimeType('image/png')));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => 'gif'])->getFormat($this->getImageByMimeType('image/gif')));
        $this->assertSame('bmp', $this->encoder->setParams(['fm' => 'bmp'])->getFormat($this->getImageByMimeType('image/bmp')));

        // Make sure 'fm' parameter takes precedence
        $this->assertSame('png', $this->encoder->setParams(['fm' => 'png'])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => 'gif'])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('bmp', $this->encoder->setParams(['fm' => 'bmp'])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('pjpg', $this->encoder->setParams(['fm' => 'pjpg'])->getFormat($this->getImageByMimeType('image/jpeg')));

        // Make sure we keep the current format if no format is provided
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('png', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/png')));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/gif')));
        $this->assertSame('bmp', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/bmp')));
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => 'null'])->getFormat($this->getImageByMimeType('image/pjpeg')));

        $this->assertSame('jpg', $this->encoder->setParams(['fm' => ''])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('png', $this->encoder->setParams(['fm' => ''])->getFormat($this->getImageByMimeType('image/png')));
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => 'invalid'])->getFormat($this->getImageByMimeType('image/jpeg')));

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('webp', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/webp')));
            $this->assertSame('webp', $this->encoder->setParams(['fm' => 'webp'])->getFormat($this->getImageByMimeType('image/jpeg')));
        }

        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('avif', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/avif')));
            $this->assertSame('avif', $this->encoder->setParams(['fm' => 'avif'])->getFormat($this->getImageByMimeType('image/jpeg')));
        }
    }

    protected function getImageByMimeType(string $mimeType): ImageInterface
    {
        return \Mockery::mock(ImageInterface::class, function ($mock) use ($mimeType) {
            $this->assertMediaType($mock, $mimeType);
        });
    }

    public function testGetQuality(): void
    {
        $this->assertSame(100, $this->encoder->setParams(['q' => '100'])->getQuality());
        $this->assertSame(100, $this->encoder->setParams(['q' => 100])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => null])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => 'a'])->getQuality());
        $this->assertSame(50, $this->encoder->setParams(['q' => '50.50'])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => '-1'])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => '101'])->getQuality());
    }

    /**
     * Test functions that require the imagick extension.
     */
    public function testWithImagick(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped(
                'The imagick extension is not available.'
            );
        }
        $manager = ImageManager::imagick();

        // These need to be recreated with the imagick driver selected in the manager
        $this->jpg = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/jpeg'))->toFilePointer());
        $this->png = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/png'))->toFilePointer());
        $this->gif = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/gif'))->toFilePointer());
        $this->heic = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/heic'))->toFilePointer());
        $this->tif = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/tiff'))->toFilePointer());

        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->jpg)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->png)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->gif)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->heic)));
    }

    public function testSupportedFormats(): void
    {
        $expected = [
            'avif' => 'image/avif',
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'heic' => 'image/heic',
            'jpg' => 'image/jpeg',
            'pjpg' => 'image/pjpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'webp' => 'image/webp',
        ];

        $this->assertSame($expected, Encoder::supportedFormats());
    }

    public function testSupportedMediaTypes(): void
    {
        $expected = [
            'avif' => MediaType::IMAGE_AVIF,
            'bmp' => MediaType::IMAGE_BMP,
            'gif' => MediaType::IMAGE_GIF,
            'heic' => MediaType::IMAGE_HEIC,
            'jpg' => MediaType::IMAGE_JPEG,
            'pjpg' => MediaType::IMAGE_PJPEG,
            'png' => MediaType::IMAGE_PNG,
            'tiff' => MediaType::IMAGE_TIFF,
            'webp' => MediaType::IMAGE_WEBP,
        ];

        $this->assertSame($expected, Encoder::supportedMediaTypes());
    }

    protected function getMime(EncodedImageInterface $image): string
    {
        return $image->mediaType();
    }

    /**
     * Creates an assertion to check media type.
     *
     * @param Mockery\Mock $mock
     *
     * @psalm-suppress MoreSpecificReturnType
     */
    protected function assertMediaType($mock, string $mediaType): Mockery\CompositeExpectation
    {
        /**
         * @psalm-suppress LessSpecificReturnStatement, UndefinedMagicMethod
         */
        return $mock->shouldReceive('origin')
            ->andReturn(\Mockery::mock(Origin::class, ['mediaType' => $mediaType]))
            ->shouldReceive('driver')
            ->andReturn(\Mockery::mock(DriverInterface::class, function ($mock) {
                $mock->shouldReceive('supports');
            }));
    }
}

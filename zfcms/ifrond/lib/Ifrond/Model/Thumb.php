<?php

class Ifrond_Model_Thumb
{


	protected $thumbPath;
	/**
	 * All imported objects
	 *
	 * An array of imported plugin objects
	 *
	 * @var array
	 */
	protected $imported;
	/**
	 * All imported object functions
	 *
	 * An array of all methods added to this class by imported plugin objects
	 *
	 * @var array
	 */
	protected $importedFunctions;
	/**
	 * The last error message raised
	 *
	 * @var string
	 */
	protected $errorMessage;
	/**
	 * Whether or not the current instance has any errors
	 *
	 * @var bool
	 */
	protected $hasError;
	/**
	 * The name of the file we're manipulating
	 *
	 * This must include the path to the file (absolute paths recommended)
	 *
	 * @var string
	 */
	protected $fileName;
	/**
	 * What the file format is (mime-type)
	 *
	 * @var string
	 */
	protected $format;
	/**
	 * Whether or not the image is hosted remotely
	 *
	 * @var bool
	 */
	protected $remoteImage;
	/**
	 * Whether or not the current image is an actual file, or the raw file data
	 *
	 * By "raw file data" it's meant that we're actually passing the result of something
	 * like file_get_contents() or perhaps from a database blob
	 *
	 * @var bool
	 */
	protected $isDataStream;


	/* The prior image (before manipulation)
	 *
	 * @var resource
	 */
	protected $oldImage;
	/**
	 * The working image (used during manipulation)
	 *
	 * @var resource
	 */
	protected $workingImage;
	/**
	 * The current dimensions of the image
	 *
	 * @var array
	 */
	protected $currentDimensions;
	/**
	 * The new, calculated dimensions of the image
	 *
	 * @var array
	 */
	protected $newDimensions;
	/**
	 * The options for this class
	 *
	 * This array contains various options that determine the behavior in
	 * various functions throughout the class.  Functions note which specific
	 * option key / values are used in their documentation
	 *
	 * @var array
	 */
	protected $options;
	/**
	 * The maximum width an image can be after resizing (in pixels)
	 *
	 * @var int
	 */
	protected $maxWidth;
	/**
	 * The maximum height an image can be after resizing (in pixels)
	 *
	 * @var int
	 */
	protected $maxHeight;
	/**
	 * The percentage to resize the image by
	 *
	 * @var int
	 */
	protected $percent;

	/**
	 * Class Constructor
	 *
	 * @return GdThumb
	 * @param string $fileName
	 */
	public function __construct ($fileName, $options = array(), $isDataStream = false)
	{
		$this->imported				= array();
		$this->importedFunctions	= array();
		$this->errorMessage			= null;
		$this->hasError				= false;
		$this->fileName				= $fileName;
		$this->remoteImage			= false;
		$this->isDataStream			= $isDataStream;

		$this->fileExistsAndReadable();

		$this->determineFormat();

		if ($this->isDataStream === false)
		{
			$this->verifyFormatCompatiblity();
		}

		switch ($this->format)
		{
			case 'GIF':
				$this->oldImage = imagecreatefromgif($this->fileName);
				break;
			case 'JPG':
				$this->oldImage = imagecreatefromjpeg($this->fileName);
				break;
			case 'PNG':
				$this->oldImage = imagecreatefrompng($this->fileName);
				break;
			case 'STRING':
				$this->oldImage = imagecreatefromstring($this->fileName);
				break;
		}

		$this->currentDimensions = array
		(
			'width' 	=> imagesx($this->oldImage),
			'height'	=> imagesy($this->oldImage)
		);

		$this->setOptions($options);

		// TODO: Port gatherImageMeta to a separate function that can be called to extract exif data
	}


	/**
	 * Imports plugins in $registry to the class
	 *
	 * @param array $registry
	 */
	public function importPlugins ($registry)
	{
		foreach ($registry as $plugin => $meta)
		{
			$this->imports($plugin);
		}
	}

	/**
	 * Imports a plugin
	 *
	 * This is where all the plugins magic happens!  This function "loads" the plugin functions, making them available as
	 * methods on the class.
	 *
	 * @param string $object The name of the object to import / "load"
	 */
	protected function imports ($object)
	{
		// the new object to import
		$newImport 			= new $object();
		// the name of the new object (class name)
		$importName			= get_class($newImport);
		// the new functions to import
		$importFunctions 	= get_class_methods($newImport);

		// add the object to the registry
		array_push($this->imported, array($importName, $newImport));

		// add the methods to the registry
		foreach ($importFunctions as $key => $functionName)
		{
			$this->importedFunctions[$functionName] = &$newImport;
		}
	}

	/**
	 * Checks to see if $this->fileName exists and is readable
	 *
	 */
	protected function fileExistsAndReadable ()
	{
		if ($this->isDataStream === true)
		{
			return;
		}

		if (stristr($this->fileName, 'http://') !== false)
		{
			$this->remoteImage = true;
			return;
		}

		if (!file_exists($this->fileName))
		{
			$this->triggerError('Image file not found: ' . $this->fileName);
		}
		elseif (!is_readable($this->fileName))
		{
			$this->triggerError('Image file not readable: ' . $this->fileName);
		}
	}

	/**
	 * Sets $this->errorMessage to $errorMessage and throws an exception
	 *
	 * Also sets $this->hasError to true, so even if the exceptions are caught, we don't
	 * attempt to proceed with any other functions
	 *
	 * @param string $errorMessage
	 */
	protected function triggerError ($errorMessage)
	{
		$this->hasError 	= true;
		$this->errorMessage	= $errorMessage;

		throw new Exception ($errorMessage);
	}

	/**
	 * Calls plugin / imported functions
	 *
	 * This is also where a fair amount of plugins magaic happens.  This magic method is called whenever an "undefined" class
	 * method is called in code, and we use that to call an imported function.
	 *
	 * You should NEVER EVER EVER invoke this function manually.  The universe will implode if you do... seriously ;)
	 *
	 * @param string $method
	 * @param array $args
	 */
	public function __call ($method, $args)
	{
		if( array_key_exists($method, $this->importedFunctions))
		{
			$args[] = $this;
			return call_user_func_array(array($this->importedFunctions[$method], $method), $args);
		}

		throw new BadMethodCallException ('Call to undefined method/class function: ' . $method);
	}

	/**
	 * Returns $imported.
	 * @see ThumbBase::$imported
	 * @return array
	 */
	public function getImported ()
	{
		return $this->imported;
	}

	/**
	 * Returns $importedFunctions.
	 * @see ThumbBase::$importedFunctions
	 * @return array
	 */
	public function getImportedFunctions ()
	{
		return $this->importedFunctions;
	}

	/**
	 * Returns $errorMessage.
	 *
	 * @see ThumbBase::$errorMessage
	 */
	public function getErrorMessage ()
	{
		return $this->errorMessage;
	}

	/**
	 * Sets $errorMessage.
	 *
	 * @param object $errorMessage
	 * @see ThumbBase::$errorMessage
	 */
	public function setErrorMessage ($errorMessage)
	{
		$this->errorMessage = $errorMessage;
	}

	/**
	 * Returns $fileName.
	 *
	 * @see ThumbBase::$fileName
	 */
	public function getFileName ()
	{
		return $this->fileName;
	}

	/**
	 * Sets $fileName.
	 *
	 * @param object $fileName
	 * @see ThumbBase::$fileName
	 */
	public function setFileName ($fileName)
	{
		$this->fileName = $fileName;
	}

	/**
	 * Returns $format.
	 *
	 * @see ThumbBase::$format
	 */
	public function getFormat ()
	{
		return $this->format;
	}

	/**
	 * Sets $format.
	 *
	 * @param object $format
	 * @see ThumbBase::$format
	 */
	public function setFormat ($format)
	{
		$this->format = $format;
	}

	/**
	 * Returns $hasError.
	 *
	 * @see ThumbBase::$hasError
	 */
	public function getHasError ()
	{
		return $this->hasError;
	}

	/**
	 * Sets $hasError.
	 *
	 * @param object $hasError
	 * @see ThumbBase::$hasError
	 */
	public function setHasError ($hasError)
	{
		$this->hasError = $hasError;
	}



	/**
	 * Class Destructor
	 *
	 */
	public function __destruct ()
	{
		if (is_resource($this->oldImage))
		{
			imagedestroy($this->oldImage);
		}

		if (is_resource($this->workingImage))
		{
			imagedestroy($this->workingImage);
		}
	}

	##############################
	# ----- API FUNCTIONS ------ #
	##############################

	/**
	 * Resizes an image to be no larger than $maxWidth or $maxHeight
	 *
	 * If either param is set to zero, then that dimension will not be considered as a part of the resize.
	 * Additionally, if $this->options['resizeUp'] is set to true (false by default), then this function will
	 * also scale the image up to the maximum dimensions provided.
	 *
	 * @param int $maxWidth The maximum width of the image in pixels
	 * @param int $maxHeight The maximum height of the image in pixels
	 * @return GdThumb
	 */
	public function resize ($maxWidth = 0, $maxHeight = 0)
	{
		// make sure our arguments are valid
		if (!is_numeric($maxWidth))
		{
			throw new InvalidArgumentException('$maxWidth must be numeric');
		}

		if (!is_numeric($maxHeight))
		{
			throw new InvalidArgumentException('$maxHeight must be numeric');
		}

		// make sure we're not exceeding our image size if we're not supposed to
		if ($this->options['resizeUp'] === false)
		{
			$this->maxHeight	= (intval($maxHeight) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $maxHeight;
			$this->maxWidth		= (intval($maxWidth) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $maxWidth;
		}
		else
		{
			$this->maxHeight	= intval($maxHeight);
			$this->maxWidth		= intval($maxWidth);
		}

		// get the new dimensions...
		$this->calcImageSize($this->currentDimensions['width'], $this->currentDimensions['height']);

		// create the working image
		if (function_exists('imagecreatetruecolor'))
		{
			$this->workingImage = imagecreatetruecolor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		}
		else
		{
			$this->workingImage = imagecreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		}

		$this->preserveAlpha();

		// and create the newly sized image
		imagecopyresampled
		(
		$this->workingImage,
		$this->oldImage,
		0,
		0,
		0,
		0,
		$this->newDimensions['newWidth'],
		$this->newDimensions['newHeight'],
		$this->currentDimensions['width'],
		$this->currentDimensions['height']
		);

		// update all the variables and resources to be correct
		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] 	= $this->newDimensions['newHeight'];

		return $this;
	}

	/**
	 * Adaptively Resizes the Image
	 *
	 * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the
	 * remaining overflow (from the center) to get the image to be the size specified
	 *
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @return GdThumb
	 */
	public function adaptiveResize ($width, $height)
	{
		// make sure our arguments are valid
		if (!is_numeric($width) || $width  == 0)
		{
			throw new InvalidArgumentException('$width must be numeric and greater than zero');
		}

		if (!is_numeric($height) || $height == 0)
		{
			throw new InvalidArgumentException('$height must be numeric and greater than zero');
		}

		// make sure we're not exceeding our image size if we're not supposed to
		if ($this->options['resizeUp'] === false)
		{
			$this->maxHeight	= (intval($height) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $height;
			$this->maxWidth		= (intval($width) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $width;
		}
		else
		{
			$this->maxHeight	= intval($height);
			$this->maxWidth		= intval($width);
		}

		$this->calcImageSizeStrict($this->currentDimensions['width'], $this->currentDimensions['height']);

		// resize the image to be close to our desired dimensions
		$this->resize($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);

		// reset the max dimensions...
		if ($this->options['resizeUp'] === false)
		{
			$this->maxHeight	= (intval($height) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $height;
			$this->maxWidth		= (intval($width) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $width;
		}
		else
		{
			$this->maxHeight	= intval($height);
			$this->maxWidth		= intval($width);
		}

		// create the working image
		if (function_exists('imagecreatetruecolor'))
		{
			$this->workingImage = imagecreatetruecolor($this->maxWidth, $this->maxHeight);
		}
		else
		{
			$this->workingImage = imagecreate($this->maxWidth, $this->maxHeight);
		}

		$this->preserveAlpha();

		$cropWidth	= $this->maxWidth;
		$cropHeight	= $this->maxHeight;
		$cropX 		= 0;
		$cropY 		= 0;

		// now, figure out how to crop the rest of the image...
		if ($this->currentDimensions['width'] > $this->maxWidth)
		{
			$cropX = intval(($this->currentDimensions['width'] - $this->maxWidth) / 2);
		}
		elseif ($this->currentDimensions['height'] > $this->maxHeight)
		{
			$cropY = intval(($this->currentDimensions['height'] - $this->maxHeight) / 2);
		}

		imagecopyresampled
		(
		$this->workingImage,
		$this->oldImage,
		0,
		0,
		$cropX,
		$cropY,
		$cropWidth,
		$cropHeight,
		$cropWidth,
		$cropHeight
		);

		// update all the variables and resources to be correct
		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $this->maxWidth;
		$this->currentDimensions['height'] 	= $this->maxHeight;

		return $this;
	}

	/**
	 * Resizes an image by a given percent uniformly
	 *
	 * Percentage should be whole number representation (i.e. 1-100)
	 *
	 * @param int $percent
	 * @return GdThumb
	 */
	public function resizePercent ($percent = 0)
	{
		if (!is_numeric($percent))
		{
			throw new InvalidArgumentException ('$percent must be numeric');
		}

		$this->percent = intval($percent);

		$this->calcImageSizePercent($this->currentDimensions['width'], $this->currentDimensions['height']);

		if (function_exists('imagecreatetruecolor'))
		{
			$this->workingImage = imagecreatetruecolor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		}
		else
		{
			$this->workingImage = imagecreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		}

		$this->preserveAlpha();

		ImageCopyResampled(
		$this->workingImage,
		$this->oldImage,
		0,
		0,
		0,
		0,
		$this->newDimensions['newWidth'],
		$this->newDimensions['newHeight'],
		$this->currentDimensions['width'],
		$this->currentDimensions['height']
		);

		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] 	= $this->newDimensions['newHeight'];

		return $this;
	}

	/**
	 * Crops an image from the center with provided dimensions
	 *
	 * If no height is given, the width will be used as a height, thus creating a square crop
	 *
	 * @param int $cropWidth
	 * @param int $cropHeight
	 * @return GdThumb
	 */
	public function cropFromCenter ($cropWidth, $cropHeight = null)
	{
		if (!is_numeric($cropWidth))
		{
			throw new InvalidArgumentException('$cropWidth must be numeric');
		}

		if ($cropHeight !== null && !is_numeric($cropHeight))
		{
			throw new InvalidArgumentException('$cropHeight must be numeric');
		}

		if ($cropHeight === null)
		{
			$cropHeight = $cropWidth;
		}

		$cropWidth	= ($this->currentDimensions['width'] < $cropWidth) ? $this->currentDimensions['width'] : $cropWidth;
		$cropHeight = ($this->currentDimensions['height'] < $cropHeight) ? $this->currentDimensions['height'] : $cropHeight;

		$cropX = intval(($this->currentDimensions['width'] - $cropWidth) / 2);
		$cropY = intval(($this->currentDimensions['height'] - $cropHeight) / 2);

		$this->crop($cropX, $cropY, $cropWidth, $cropHeight);

		return $this;
	}

	/**
	 * Vanilla Cropping - Crops from x,y with specified width and height
	 *
	 * @param int $startX
	 * @param int $startY
	 * @param int $cropWidth
	 * @param int $cropHeight
	 * @return GdThumb
	 */
	public function crop ($startX, $startY, $cropWidth, $cropHeight)
	{
		// validate input
		if (!is_numeric($startX))
		{
			throw new InvalidArgumentException('$startX must be numeric');
		}

		if (!is_numeric($startY))
		{
			throw new InvalidArgumentException('$startY must be numeric');
		}

		if (!is_numeric($cropWidth))
		{
			throw new InvalidArgumentException('$cropWidth must be numeric');
		}

		if (!is_numeric($cropHeight))
		{
			throw new InvalidArgumentException('$cropHeight must be numeric');
		}

		// do some calculations
		$cropWidth	= ($this->currentDimensions['width'] < $cropWidth) ? $this->currentDimensions['width'] : $cropWidth;
		$cropHeight = ($this->currentDimensions['height'] < $cropHeight) ? $this->currentDimensions['height'] : $cropHeight;

		// ensure everything's in bounds
		if (($startX + $cropWidth) > $this->currentDimensions['width'])
		{
			$startX = ($this->currentDimensions['width'] - $cropWidth);

		}

		if (($startY + $cropHeight) > $this->currentDimensions['height'])
		{
			$startY = ($this->currentDimensions['height'] - $cropHeight);
		}

		if ($startX < 0)
		{
			$startX = 0;
		}

		if ($startY < 0)
		{
			$startY = 0;
		}

		// create the working image
		if (function_exists('imagecreatetruecolor'))
		{
			$this->workingImage = imagecreatetruecolor($cropWidth, $cropHeight);
		}
		else
		{
			$this->workingImage = imagecreate($cropWidth, $cropHeight);
		}

		$this->preserveAlpha();

		imagecopyresampled
		(
		$this->workingImage,
		$this->oldImage,
		0,
		0,
		$startX,
		$startY,
		$cropWidth,
		$cropHeight,
		$cropWidth,
		$cropHeight
		);

		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $cropWidth;
		$this->currentDimensions['height'] 	= $cropHeight;

		return $this;
	}

	/**
	 * Rotates image either 90 degrees clockwise or counter-clockwise
	 *
	 * @param string $direction
	 * @retunrn GdThumb
	 */
	public function rotateImage ($direction = 'CW')
	{
		if ($direction == 'CW')
		{
			$this->rotateImageNDegrees(90);
		}
		else
		{
			$this->rotateImageNDegrees(-90);
		}

		return $this;
	}

	/**
	 * Rotates image specified number of degrees
	 *
	 * @param int $degrees
	 * @return GdThumb
	 */
	public function rotateImageNDegrees ($degrees)
	{
		if (!is_numeric($degrees))
		{
			throw new InvalidArgumentException('$degrees must be numeric');
		}

		if (!function_exists('imagerotate'))
		{
			throw new RuntimeException('Your version of GD does not support image rotation.');
		}

		$this->workingImage = imagerotate($this->oldImage, $degrees, 0);
			
		$newWidth 							= $this->currentDimensions['height'];
		$newHeight 							= $this->currentDimensions['width'];
		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $newWidth;
		$this->currentDimensions['height'] 	= $newHeight;

		return $this;
	}

	/**
	 * Shows an image
	 *
	 * This function will show the current image by first sending the appropriate header
	 * for the format, and then outputting the image data. If headers have already been sent,
	 * a runtime exception will be thrown
	 *
	 * @param bool $rawData Whether or not the raw image stream should be output
	 * @return GdThumb
	 */
	public function show ($rawData = false)
	{
		if (headers_sent())
		{
			throw new RuntimeException('Cannot show image, headers have already been sent');
		}

		switch ($this->format)
		{
			case 'GIF':
				if ($rawData === false)
				{
					header('Content-type: image/gif');
				}
				imagegif($this->oldImage);
				break;
			case 'JPG':
				if ($rawData === false)
				{
					header('Content-type: image/jpeg');
				}
				imagejpeg($this->oldImage, null, $this->options['jpegQuality']);
				break;
			case 'PNG':
			case 'STRING':
				if ($rawData === false)
				{
					header('Content-type: image/png');
				}
				imagepng($this->oldImage);
				break;
		}

		return $this;
	}

	/**
	 * Returns the Working Image as a String
	 *
	 * This function is useful for getting the raw image data as a string for storage in
	 * a database, or other similar things.
	 *
	 * @return string
	 */
	public function getImageAsString ()
	{
		$data = null;
		ob_start();
		$this->show(true);
		$data = ob_get_contents();
		ob_end_clean();

		return $data;
	}

	/**
	 * Saves an image
	 *
	 * This function will make sure the target directory is writeable, and then save the image.
	 *
	 * If the target directory is not writeable, the function will try to correct the permissions (if allowed, this
	 * is set as an option ($this->options['correctPermissions']).  If the target cannot be made writeable, then a
	 * RuntimeException is thrown.
	 *
	 * TODO: Create additional paramter for color matte when saving images with alpha to non-alpha formats (i.e. PNG => JPG)
	 *
	 * @param string $fileName The full path and filename of the image to save
	 * @param string $format The format to save the image in (optional, must be one of [GIF,JPG,PNG]
	 * @return GdThumb
	 */
	public function save ($fileName, $format = null)
	{
		$validFormats = array('GIF', 'JPG', 'PNG');
		$format = ($format !== null) ? strtoupper($format) : $this->format;

		if (!in_array($format, $validFormats))
		{
			throw new InvalidArgumentException ('Invalid format type specified in save function: ' . $format);
		}

		// make sure the directory is writeable
		if (!is_writeable(dirname($fileName)))
		{
			// try to correct the permissions
			if ($this->options['correctPermissions'] === true)
			{
				@chmod(dirname($fileName), 0777);

				// throw an exception if not writeable
				if (!is_writeable(dirname($fileName)))
				{
					throw new RuntimeException ('File is not writeable, and could not correct permissions: ' . $fileName);
				}
			}
			// throw an exception if not writeable
			else
			{
				throw new RuntimeException ('File not writeable: ' . $fileName);
			}
		}

		switch ($format)
		{
			case 'GIF':
				imagegif($this->oldImage, $fileName);
				break;
			case 'JPG':
				imagejpeg($this->oldImage, $fileName, $this->options['jpegQuality']);
				break;
			case 'PNG':
				imagepng($this->oldImage, $fileName);
				break;
		}

		$this->thumbPath = $fileName;

		return $this;
	}

	public function saveQuick () {
		$fileName = $this->getFileName();
		$dims = $this->getCurrentDimensions();
		$path_parts = pathinfo($fileName);
		$thumbsDir = $path_parts['dirname'].'/thumbs';
		if (!is_dir($thumbsDir)) {
			if (!mkdir($thumbsDir)) {
				throw new RuntimeException ('Can not be created dir: ' . $thumbsDir);
				return false;
			}
		}
		$path_parts['extension'];
		$path_parts['filename'];
		$thumbName = $thumbsDir.'/'
		.$path_parts['filename']
		.'_'.$dims['width'].'x'.$dims['height']
		.'.'.strtolower($path_parts['extension']);
		if (!is_file($thumbName)) $this->save($thumbName);
		else $this->thumbPath = $thumbName;
		return $this;
	}

	public function getImageAsTag ($extraAttrs = array())
	{
		$dims = $this->getCurrentDimensions();
		$url = $this->getThumbUrl();
		$attribs = array();
		foreach ($extraAttrs as $k => $t) {
			$attribs[] = $k.'="'.$t.'"';
		}
		$tag = '<img src="'.$url.'" 
				width="'.$dims['width'].'" height="'.$dims['height'].'" '
				.implode(' ', $attribs).' />';		
		return $tag;
	}
	
	public function getThumbUrl ()
	{
		$fileName = $this->thumbPath;
		$url = str_replace(PATH_WEBROOT, '', $fileName);
		$url = str_replace('\\', '/', $url);
		return $url;
	}

	#################################
	# ----- GETTERS / SETTERS ----- #
	#################################

	/**
	 * Sets $this->options to $options
	 *
	 * @param array $options
	 */
	public function setOptions ($options = array())
	{
		// make sure we've got an array for $this->options (could be null)
		if (!is_array($this->options))
		{
			$this->options = array();
		}

		// make sure we've gotten a proper argument
		if (!is_array($options))
		{
			throw new InvalidArgumentException ('setOptions requires an array');
		}

		// we've yet to init the default options, so create them here
		if (sizeof($this->options) == 0)
		{
			$defaultOptions = array
			(
				'resizeUp'				=> false,
				'jpegQuality'			=> 100,
				'correctPermissions'	=> false,
				'preserveAlpha'			=> true,
				'alphaMaskColor'		=> array (255, 255, 255),
				'preserveTransparency'	=> true,
				'transparencyMaskColor'	=> array (0, 0, 0)
			);
		}
		// otherwise, let's use what we've got already
		else
		{
			$defaultOptions = $this->options;
		}

		$this->options = array_merge($defaultOptions, $options);
	}

	/**
	 * Returns $currentDimensions.
	 *
	 * @see GdThumb::$currentDimensions
	 */
	public function getCurrentDimensions ()
	{
		return $this->currentDimensions;
	}

	/**
	 * Sets $currentDimensions.
	 *
	 * @param object $currentDimensions
	 * @see GdThumb::$currentDimensions
	 */
	public function setCurrentDimensions ($currentDimensions)
	{
		$this->currentDimensions = $currentDimensions;
	}

	/**
	 * Returns $maxHeight.
	 *
	 * @see GdThumb::$maxHeight
	 */
	public function getMaxHeight ()
	{
		return $this->maxHeight;
	}

	/**
	 * Sets $maxHeight.
	 *
	 * @param object $maxHeight
	 * @see GdThumb::$maxHeight
	 */
	public function setMaxHeight ($maxHeight)
	{
		$this->maxHeight = $maxHeight;
	}

	/**
	 * Returns $maxWidth.
	 *
	 * @see GdThumb::$maxWidth
	 */
	public function getMaxWidth ()
	{
		return $this->maxWidth;
	}

	/**
	 * Sets $maxWidth.
	 *
	 * @param object $maxWidth
	 * @see GdThumb::$maxWidth
	 */
	public function setMaxWidth ($maxWidth)
	{
		$this->maxWidth = $maxWidth;
	}

	/**
	 * Returns $newDimensions.
	 *
	 * @see GdThumb::$newDimensions
	 */
	public function getNewDimensions ()
	{
		return $this->newDimensions;
	}

	/**
	 * Sets $newDimensions.
	 *
	 * @param object $newDimensions
	 * @see GdThumb::$newDimensions
	 */
	public function setNewDimensions ($newDimensions)
	{
		$this->newDimensions = $newDimensions;
	}

	/**
	 * Returns $options.
	 *
	 * @see GdThumb::$options
	 */
	public function getOptions ()
	{
		return $this->options;
	}

	/**
	 * Returns $percent.
	 *
	 * @see GdThumb::$percent
	 */
	public function getPercent ()
	{
		return $this->percent;
	}

	/**
	 * Sets $percent.
	 *
	 * @param object $percent
	 * @see GdThumb::$percent
	 */
	public function setPercent ($percent)
	{
		$this->percent = $percent;
	}

	/**
	 * Returns $oldImage.
	 *
	 * @see GdThumb::$oldImage
	 */
	public function getOldImage ()
	{
		return $this->oldImage;
	}

	/**
	 * Sets $oldImage.
	 *
	 * @param object $oldImage
	 * @see GdThumb::$oldImage
	 */
	public function setOldImage ($oldImage)
	{
		$this->oldImage = $oldImage;
	}

	/**
	 * Returns $workingImage.
	 *
	 * @see GdThumb::$workingImage
	 */
	public function getWorkingImage ()
	{
		return $this->workingImage;
	}

	/**
	 * Sets $workingImage.
	 *
	 * @param object $workingImage
	 * @see GdThumb::$workingImage
	 */
	public function setWorkingImage ($workingImage)
	{
		$this->workingImage = $workingImage;
	}


	#################################
	# ----- UTILITY FUNCTIONS ----- #
	#################################

	/**
	 * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
	 *
	 * @return array
	 * @param int $width
	 * @param int $height
	 */
	protected function calcWidth ($width, $height)
	{
		$newWidthPercentage	= (100 * $this->maxWidth) / $width;
		$newHeight			= ($height * $newWidthPercentage) / 100;

		return array
		(
			'newWidth'	=> intval($this->maxWidth),
			'newHeight'	=> intval($newHeight)
		);
	}

	/**
	 * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
	 *
	 * @return array
	 * @param int $width
	 * @param int $height
	 */
	protected function calcHeight ($width, $height)
	{
		$newHeightPercentage	= (100 * $this->maxHeight) / $height;
		$newWidth 				= ($width * $newHeightPercentage) / 100;

		return array
		(
			'newWidth'	=> ceil($newWidth),
			'newHeight'	=> ceil($this->maxHeight)
		);
	}

	/**
	 * Calculates a new width and height for the image based on $this->percent and the provided dimensions
	 *
	 * @return array
	 * @param int $width
	 * @param int $height
	 */
	protected function calcPercent ($width, $height)
	{
		$newWidth	= ($width * $this->percent) / 100;
		$newHeight	= ($height * $this->percent) / 100;

		return array
		(
			'newWidth'	=> ceil($newWidth),
			'newHeight'	=> ceil($newHeight)
		);
	}

	/**
	 * Calculates the new image dimensions
	 *
	 * These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight
	 *
	 * @param int $width
	 * @param int $height
	 */
	protected function calcImageSize ($width, $height)
	{
		$newSize = array
		(
			'newWidth'	=> $width,
			'newHeight'	=> $height
		);

		if ($this->maxWidth > 0)
		{
			$newSize = $this->calcWidth($width, $height);

			if ($this->maxHeight > 0 && $newSize['newHeight'] > $this->maxHeight)
			{
				$newSize = $this->calcHeight($newSize['newWidth'], $newSize['newHeight']);
			}
		}

		if ($this->maxHeight > 0)
		{
			$newSize = $this->calcHeight($width, $height);

			if ($this->maxWidth > 0 && $newSize['newWidth'] > $this->maxWidth)
			{
				$newSize = $this->calcWidth($newSize['newWidth'], $newSize['newHeight']);
			}
		}

		$this->newDimensions = $newSize;
	}

	/**
	 * Calculates new image dimensions, not allowing the width and height to be less than either the max width or height
	 *
	 * @param int $width
	 * @param int $height
	 */
	protected function calcImageSizeStrict ($width, $height)
	{
		// first, we need to determine what the longest resize dimension is..
		if ($this->maxWidth >= $this->maxHeight)
		{
			// and determine the longest original dimension
			if ($width > $height)
			{
				$newDimensions = $this->calcHeight($width, $height);

				if ($newDimensions['newWidth'] < $this->maxWidth)
				{
					$newDimensions = $this->calcWidth($width, $height);
				}
			}
			elseif ($height >= $width)
			{
				$newDimensions = $this->calcWidth($width, $height);

				if ($newDimensions['newHeight'] < $this->maxHeight)
				{
					$newDimensions = $this->calcHeight($width, $height);
				}
			}
		}
		elseif ($this->maxHeight > $this->maxWidth)
		{
			if ($width >= $height)
			{
				$newDimensions = $this->calcWidth($width, $height);

				if ($newDimensions['newHeight'] < $this->maxHeight)
				{
					$newDimensions = $this->calcHeight($width, $height);
				}
			}
			elseif ($height > $width)
			{
				$newDimensions = $this->calcHeight($width, $height);

				if ($newDimensions['newWidth'] < $this->maxWidth)
				{
					$newDimensions = $this->calcWidth($width, $height);
				}
			}
		}

		$this->newDimensions = $newDimensions;
	}

	/**
	 * Calculates new dimensions based on $this->percent and the provided dimensions
	 *
	 * @param int $width
	 * @param int $height
	 */
	protected function calcImageSizePercent ($width, $height)
	{
		if ($this->percent > 0)
		{
			$this->newDimensions = $this->calcPercent($width, $height);
		}
	}

	/**
	 * Determines the file format by mime-type
	 *
	 * This function will throw exceptions for invalid images / mime-types
	 *
	 */
	protected function determineFormat ()
	{
		if ($this->isDataStream === true)
		{
			$this->format = 'STRING';
			return;
		}

		$formatInfo = getimagesize($this->fileName);

		// non-image files will return false
		if ($formatInfo === false)
		{
			if ($this->remoteImage)
			{
				$this->triggerError('Could not determine format of remote image: ' . $this->fileName);
			}
			else
			{
				$this->triggerError('File is not a valid image: ' . $this->fileName);
			}

			// make sure we really stop execution
			return;
		}

		$mimeType = isset($formatInfo['mime']) ? $formatInfo['mime'] : null;

		switch ($mimeType)
		{
			case 'image/gif':
				$this->format = 'GIF';
				break;
			case 'image/jpeg':
				$this->format = 'JPG';
				break;
			case 'image/png':
				$this->format = 'PNG';
				break;
			default:
				$this->triggerError('Image format not supported: ' . $mimeType);
		}
	}

	/**
	 * Makes sure the correct GD implementation exists for the file type
	 *
	 */
	protected function verifyFormatCompatiblity ()
	{
		$isCompatible 	= true;
		$gdInfo			= gd_info();

		switch ($this->format)
		{
			case 'GIF':
				$isCompatible = $gdInfo['GIF Create Support'];
				break;
			case 'JPG':
				$isCompatible = (isset($gdInfo['JPG Support']) || isset($gdInfo['JPEG Support'])) ? true : false;
				break;
			case 'PNG':
				$isCompatible = $gdInfo[$this->format . ' Support'];
				break;
			default:
				$isCompatible = false;
		}

		if (!$isCompatible)
		{
			// one last check for "JPEG" instead
			$isCompatible = $gdInfo['JPEG Support'];

			if (!$isCompatible)
			{
				$this->triggerError('Your GD installation does not support ' . $this->format . ' image types');
			}
		}
	}

	/**
	 * Preserves the alpha or transparency for PNG and GIF files
	 *
	 * Alpha / transparency will not be preserved if the appropriate options are set to false.
	 * Also, the GIF transparency is pretty skunky (the results aren't awesome), but it works like a
	 * champ... that's the nature of GIFs tho, so no huge surprise.
	 *
	 * This functionality was originally suggested by commenter Aimi (no links / site provided) - Thanks! :)
	 *
	 */
	protected function preserveAlpha ()
	{
		if ($this->format == 'PNG' && $this->options['preserveAlpha'] === true)
		{
			imagealphablending($this->workingImage, false);

			$colorTransparent = imagecolorallocatealpha
			(
			$this->workingImage,
			$this->options['alphaMaskColor'][0],
			$this->options['alphaMaskColor'][1],
			$this->options['alphaMaskColor'][2],
			0
			);

			imagefill($this->workingImage, 0, 0, $colorTransparent);
			imagesavealpha($this->workingImage, true);
		}
		// preserve transparency in GIFs... this is usually pretty rough tho
		if ($this->format == 'GIF' && $this->options['preserveTransparency'] === true)
		{
			$colorTransparent = imagecolorallocate
			(
			$this->workingImage,
			$this->options['transparencyMaskColor'][0],
			$this->options['transparencyMaskColor'][1],
			$this->options['transparencyMaskColor'][2]
			);

			imagecolortransparent($this->workingImage, $colorTransparent);
			imagetruecolortopalette($this->workingImage, true, 256);
		}
	}
}
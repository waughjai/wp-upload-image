WP Upload Image
=========================

Class for autogenerating HTML for WordPress media image with just the ID.

Just pass in ID & WordPress will automatically generate HTMLImage object based on ID in media. If no item with the given ID is in media database, then it just creates an empty HTMLImage object.

## Error Handling

Like HTMLImage, if “show-version” is set on ( the default ) & the server can’t access the file to get its version information, the constructor will throw a MissingFileException that includes fallback content that is the equivalent o’ the object with “show-version” set off & a list o’ missing files. Read HTMLImage’s documentation for mo’ information.

If WordPress can’t find the media ID in its media database, then the constructor will throw a WPMissingMediaException, which keeps a list o’ IDs passed into it ’pon construction, which can be reached through its getMissingIDs() method.

## Changelog

### 0.5.2
* Fix URL problems with date-based URLs

### 0.5.1
* Make absoluteToLocal method public so WPUploadPicture can use it

### 0.5.0
* Integrate HTMLImages sizes autogeneration & shorthand format

### 0.4.1
* Fix Error Handling Bugs for Responsive Images

### 0.4.0
* Add Error-handling for missing media ID

### 0.3.0
* Update error handling.

### 0.2.2
* Make getFormattedURL Method Parameters Less Asinine

### 0.2.1
* Make getFormattedURL Method Public So WPUploadPicture Can Access It

### 0.2.0
* Implement versioning for fixing cache poisoning

### 0.1.2
* Fix WordPress Uploads Compatibility Bug

### 0.1.1
* Fix Inaccurate Base URL

### 0.1.0
* Initial version

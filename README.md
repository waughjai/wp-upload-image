WP Upload Image
=========================

Class for autogenerating HTML for WordPress media image with just the ID.

Just pass in ID & WordPress will automatically generate HTMLImage object based on ID in media. If no item with the given ID is in media database, then it just creates an empty HTMLImage object.

## Error Handling

Like HTMLImage, if “show-version” is set on ( the default ) & the server can’t access the file to get its version information, the constructor will throw a MissingFileException that includes fallback content that is the equivalent o’ the object with “show-version” set off & a list o’ missing files. Read HTMLImage’s documentation for mo’ information.

If WordPress can’t find the media ID in its media database, then the constructor will throw a WPMissingMediaException, which keeps a list o’ IDs passed into it ’pon construction, which can be reached through its getMissingIDs() method.

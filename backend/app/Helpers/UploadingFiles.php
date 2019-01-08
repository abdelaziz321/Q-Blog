<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadingFiles
{
    /**
     * update the avatar file of the user.
     *
     * @param  UploadedFile $avatarFile
     * @param  string       $prevAvatarName
     * @return string       the file name that will be stored in the db
     */
    public static function uploadAvatar(UploadedFile $avatarFile, string $prevAvatarName = '')
    {
        // delete the previous avatar if exists.
        $fileName = public_path('storage/users/') . $prevAvatarName;
        if ($prevAvatarName !== '' && is_file($fileName)) {
            Storage::delete('public/users/' . $prevAvatarName);
        }

        // upload the new avatar.
        $newAvatarName = time() . '-' . $avatarFile->getClientOriginalName();

        $avatarImage = \Image::make($avatarFile)->fit(300, 300)->stream();
        Storage::put('public/users/' . $newAvatarName, $avatarImage);

        return $newAvatarName;
    }

    /**
     * upload the given post caption
     *
     * @param  UploadedFile $caption
     * @param  string       $prevCaptionName
     * @return string       the file name that will be stored in the db
     */
    public static function uploadCaption(UploadedFile $caption, string $prevCaptionName = '')
    {
        // delete the previous caption if exists.
        if ($prevCaptionName !== '') {
            self::removeCaption($prevCaptionName);
        }

        // get filename with extension
        $filenamewithextension = $caption->getClientOriginalName();

        // get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

        // get file extension
        $extension = $caption->getClientOriginalExtension();

        // filename to store
        $filenameToStore = $filename . '-' . time() . '_.' . $extension;

        // resize and upload the caption
        $captionImage = \Image::make($caption)->fit(900, 600)->stream();
        Storage::put('public/posts/captions/' . $filenameToStore, $captionImage);

        return $filenameToStore;
    }

    /**
     * remove the given caption post.
     *
     * @param  string $caption
     * @return void
     */
    public static function removeCaption(string $caption)
    {
        if (is_file(public_path('storage/posts/captions/') . $caption)) {
            Storage::delete('public/posts/captions/' . $caption);
        }
    }

    /**
     * upload the images from the <img /> tags inside the given post body.
     *
     * @param  string $body
     * @return string
     */
    public static function uploadBodyImages(string $body, string $prevBody = '')
    {
        // all the images which the user want to keep when update the post body
        $newImages = [];

        if ($prevBody !== '') {
            # now we have all the images from the previous post body
            $prevImages = self::getImages($prevBody);
        }

        $dom = new \domdocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getelementsbytagname('img');

        foreach($images as $key => $img) {
            $data = $img->getattribute('src');
            # if the img src is base64 encoded.
            if (strpos($data, 'data:image') === 0) {
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);

                $data = base64_decode($data);

                $filenameToStore = time() . $key . '.png';
                Storage::put('public/posts/images/' . $filenameToStore, $data);

                $img->removeattribute('src');
                $img->setattribute('src', 'posts/images/' . $filenameToStore);
            }
            # if the image is from this server
            else if (strpos($data, 'posts/images') === 0) {
                $newImages[] = $data;
            }
        }

        // remove the imges which resulted from $prevImages - $newImages
        if (isset($prevImages)) {
            $deleteImages = array_diff($prevImages, $newImages);
            foreach ($deleteImages as $image) {
                if (is_file(public_path('storage/') . $image)) {
                    Storage::delete('public/' . $image);
                }
            }
        }

        return $dom->savehtml();
    }

    /**
     * remove the images from the <img /> tags inside the given post body.
     *
     * @param  string $body
     * @return void
     */
    public static function removeBodyImages(string $body)
    {
        $dom = new \domdocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getelementsbytagname('img');

        foreach($images as $key => $img) {
            # contain string e.g. 'posts/images/________.png'
            $imageSrc = $img->getattribute('src');

            if (is_file(public_path('storage/') . $imageSrc)) {
                Storage::delete('public/' . $imageSrc);
            }
        }
    }

    /**
     * get the images from the given html body
     * the returned array will be something like:
     * [
     *      'posts/images/________.png',
     *      'posts/images/________.png',
     *      'posts/images/________.png'
     * ]
     *
     * @param  string $body
     * @return array
     */
    private static function getImages(string $body)
    {
        // this array will contains all src's of <img />'s  inside the $body html
        $srcs = [];

        $dom = new \domdocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getelementsbytagname('img');

        foreach($images as $key => $img) {
            $src = $img->getattribute('src');
            # if the image is from this server
            if (strpos($src, 'posts/images') === 0) {
                $srcs[] = $src;
            }

            return $srcs;
        }
    }
}

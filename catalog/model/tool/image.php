<?php
class ModelToolImage extends Model
{
    public function resize($filename, $width, $height, $params = [])
    {
        if(strpos($filename, DIR_IMAGE) !== false) $filename = str_replace(DIR_IMAGE, '', $filename);
        if (!is_file(DIR_IMAGE . $filename) ) { //TODO Revisar /*|| substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != DIR_IMAGE) { */
            $filename = 'placeholder.png';
        }

        if ($width && $height) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $watermark = isset($params['watermark']) && $params['watermark'] && config('config_watermark_image') && file_exists(DIR_IMAGE . config('config_watermark_image'));

            $image_old = $filename;
            if(!is_dir(DIR_IMAGE . 'cache/')) mkdir(DIR_IMAGE . 'cache/', 0775);
            $image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . ($watermark ? 'w' : '') . '.' . $extension;

            if (!is_file(DIR_IMAGE . $image_new) || (file_exists(DIR_IMAGE . $image_old) && filectime(DIR_IMAGE . $image_old) > filectime(DIR_IMAGE . $image_new))) {
                list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);

                if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
                    //return DIR_IMAGE . $image_old;
                }

                $path = '';

                $directories = explode('/', dirname($image_new));

                foreach ($directories as $directory) {
                    $path = $path . '/' . $directory;

                    if (!is_dir(DIR_IMAGE . $path)) {
                        @mkdir(DIR_IMAGE . $path, 0777);
                    }
                }

                if ($width_orig != $width || $height_orig != $height) {
                    $image = new Image(DIR_IMAGE . $image_old);
                    $method = (!empty($params['manipulation'])) ? (int)$params['manipulation'] : 0;
                    $image->resize($width, $height, $method);
                    $image->save(DIR_IMAGE . $image_new);

                    if ($watermark) {
                        $image->watermark(DIR_IMAGE . config('config_watermark_image'), config('config_watermark_position'));
                    }
                }
                else {
                    copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
                }
            }

            $image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
        }
        else {
            $image_new = $filename;
        }
        if ($this->request->server['HTTPS']) {
            return $this->config->get('config_ssl') . 'image/' . $image_new;
        } else {
            return $this->config->get('config_url') . 'image/' . $image_new;
        }
    }
}

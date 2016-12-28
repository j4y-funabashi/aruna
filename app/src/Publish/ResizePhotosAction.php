<?php

namespace Aruna\Publish;

/**
 * Class ResizePhotosAction
 * @author yourname
 */
class ResizePhotosAction
{
    public function __construct(
        $log,
        $eventStore,
        $resizer
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->resizer = $resizer;
    }

    public function __invoke()
    {
        foreach ($this->eventStore->findByExtension('media', 'jpg') as $photo_file) {
            try {
                $widths = ["600"];
                foreach ($widths as $width) {
                    if (false === strpos($photo_file['path'], "media/resized/")) {
                        $this->resizer->resize(
                            $photo_file['path'],
                            str_replace("media/", "media/resized/".$width."/", $photo_file['path']),
                            $width
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->log->critical(sprintf("Resize failed [%s] %s", $photo_file['path'], $e->getMessage()));
            }
        }
    }
}

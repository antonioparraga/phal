<?php


class __UriHtmlWriter extends __ComponentWriter {

    public function renderContent($enclosed_content, __IComponent &$component) {
        return __UriContainerWriterHelper::resolveUrl($component);
    }

}

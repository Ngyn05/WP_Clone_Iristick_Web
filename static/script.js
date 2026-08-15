(function () {
    'use strict';

    var marker = '/wp-content/themes/iristick-static-theme/static/';
    var pathname = window.location.pathname;
    var markerIndex = pathname.indexOf(marker);

    if (markerIndex === -1) {
        return;
    }

    var snapshotPath = pathname.slice(markerIndex + marker.length);
    var route = snapshotPath
        .replace(/(?:^|\/)index\.html?$/i, '')
        .replace(/\.html?$/i, '')
        .replace(/^\/+|\/+$/g, '');
    var target = '/' + (route ? route + '/' : '') + window.location.search + window.location.hash;

    window.location.replace(target);
}());

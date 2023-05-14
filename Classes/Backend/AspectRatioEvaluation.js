// noinspection JSAnnotator
return (function (value) {
    if (!value) {
        return '';
    }

    value = value.trim();

    var matches = value.match(/(\d+):(\d+)/);
    if (!matches || matches.length !== 3) {
        return '';
    }

    var width = parseInt(matches[1], 10);
    var height = parseInt(matches[2], 10);

    return (width > 0 && height > 0) ? value : '';
})(value);

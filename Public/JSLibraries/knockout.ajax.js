function ajaxGet(uri, data) {
    var request = {
        url: uri,
        type: 'GET',
        contentType: "application/json",
        accepts: "application/json",
        cache: false,
        dataType: 'json',
        data: data
    };
    return $.ajax(request);
};
function ajaxPost(uri, data) {
    var request = {
        url: uri,
        type: 'POST',
        contentType: "application/json",
        accepts: "application/json",
        cache: false,
        dataType: 'json',
        data: JSON.stringify(data)
    };
    return $.ajax(request);
};
function ajaxPut(uri, data) {
    var request = {
        url: uri,
        type: 'PUT',
        contentType: "application/json",
        accepts: "application/json",
        cache: false,
        dataType: 'json',
        data: JSON.stringify(data)
    };
    return $.ajax(request);
};
function ajaxDelete(uri) {
    var request = {
        url: uri,
        type: 'DELETE',
        contentType: "application/json",
        accepts: "application/json",
        cache: false,
        dataType: 'json'
    };
    return $.ajax(request);
};
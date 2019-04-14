/**
 * Send POST request and return to callback
 * @param {String} url to send POST request
 * @return {function} function to call once POST request is complete
 */
function post_request(url, data, callback) {
    const method = 'POST';
    var xhr = new XMLHttpRequest({mozSystem: true});
    try {
        xhr.timeout = 10 * 1000;
    } catch(err) {
        
    }

    if ("withCredentials" in xhr) {
        /* XHR for Chrome/Firefox/Opera/Safari. */
        xhr.open(method, url, true);
    } else if (typeof XDomainRequest != "undefined") {
        /* XDomainRequest for IE. */
        xhr = new XDomainRequest();
        xhr.timeout = 10 * 1000;
        xhr.open(method, url);
    } else {
        /* CORS not supported. */
        xhr = null;
    }

    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    var str = Object.keys(data).map(function(key) {
        return key + '=' + data[key];
    }).join('&');
    xhr.send(str);

    xhr.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            callback(xhr);
        } else if (this.readyState==4) {
            console.error('Attempted ' + url + ' has status ' + this.status);
        }
    }
}

function log_payment(log_url, token, post_id)
{
    var data = {
        token: token,
        post_id: post_id
    };
    post_request(log_url, data, function(xhr) {
        window.location.hash = '#somenano-paywall';
        window.location.reload(true);
    });
}
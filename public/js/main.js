(function() {
    function str_repeat(x, n) {
        var s = '';
        for (;;) {
            if (n & 1) s += x;
            n >>= 1;
            if (n) x += x;
            else break;
        }
        return s;
    }

    var loaderProgress = document.getElementById('progress');

    document.getElementsByTagName('form')[0].addEventListener('submit', function(e) {
        e = e || window.event;
        var form = e.target || e.srcElement;
        var url = form.getElementsByTagName('input')[1];

        e.preventDefault();
        e.stopPropagation();

        url.className = 'dark-shadow loading';

        var finishedLoading = false;
        var counter = 0;
        var crappyLoader = setInterval(function() {
            var text = '';
            if (counter < 5) {
                text = 'Loading crappy scenic photo';
            } else if (counter < 10) {
                text = 'Over-saturate colors';
            } else if (counter < 15) {
                text = 'Add lense flare';
            } else {
                text = 'Loading douchey hipster';
            }

            loaderProgress.className = 'active';
            loaderProgress.textContent = text + str_repeat('.', counter % 4);

            counter++;
        }, 100);

        var request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                clearInterval(crappyLoader);
                loaderProgress.className = '';

                var result = document.getElementById('result');
                if (!result) {
                    result = document.createElement('img');
                    result.src = response.imageUri;
                    result.id = 'result';

                    document.getElementById('content').appendChild(result);
                } else {
                    result.src = response.imageUri;
                }

                url.className = 'dark-shadow';
            }
        };

        var postData = 'url=' + url.value;

        request.open('POST', form.getAttribute('action'), true);
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        request.setRequestHeader("Content-type", 'application/x-www-form-urlencoded');
        request.send(postData);
    });
}());

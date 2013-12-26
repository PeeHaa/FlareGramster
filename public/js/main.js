(function() {
    document.getElementsByTagName('form')[0].addEventListener('submit', function(e) {
        e = e || window.event;
        var form = e.target || e.srcElement;
        var url = form.getElementsByTagName('input')[1];

        e.preventDefault();
        e.stopPropagation();

        url.className = 'dark-shadow loading';

        var request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                var result = document.getElementById('result');
                if (!result) {
                    result = document.createElement('img');
                    result.src = response.imageUri;
                    result.id = 'result';

                    form.appendChild(result);
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

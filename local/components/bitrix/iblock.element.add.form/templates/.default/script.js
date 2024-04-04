$(document).ready(() => {

    console.log(123);

    $('input[type="submit"]').click(function (e) {

        console.log('click');

        e.preventDefault();

        let form = document.querySelector('form');
        let formData = new FormData(form);

        for(var pair of formData.entries()) {
            console.log(pair[0]+ ', '+ pair[1]);
        }

        //form.submit();

    });

});
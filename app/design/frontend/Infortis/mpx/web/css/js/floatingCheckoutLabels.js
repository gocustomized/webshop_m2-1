let FloatLabel = function (){
    // add active class and placeholder
    function handleFocus(e){
        const target = e.target;
        target.parentNode.classList.add('active');
        if(target.getAttribute('data-placeholder')) {
            target.setAttribute('placeholder', target.getAttribute('data-placeholder'));
        }
    }

    // remove active class and placeholder
    function handleBlur(e){
        const target = e.target;
        if(!target.value) {
            target.parentNode.classList.remove('active');
        }
        target.removeAttribute('placeholder');
    }

    const handleChange = (e) => {
        const target = e.target;
        if(!target.value) {
            target.parentNode.classList.remove('active');
            target.removeAttribute('placeholder');
        }else{
            target.parentNode.classList.add('active');
        }
    };

    // register events
    function bindEvents(element) {
        const floatField = element.querySelector('input');
        floatField.addEventListener('focus', handleFocus);
        floatField.addEventListener('blur', handleBlur);
        // floatField.addEventListener('input', handleChange);
    }

    // get DOM elements
    function init() {
        const floatContainers = document.querySelectorAll('.field');
        floatContainers.forEach((element) => {
            if (!element.querySelector('input')){
                return
            }
            if(element.querySelector('input').value) {
                element.classList.add('active');
            }
            bindEvents(element);
        });
    }

    return {
        init: init
    };
}();
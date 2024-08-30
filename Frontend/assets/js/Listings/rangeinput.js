
const sliders = document.querySelectorAll('.slider');
const outputs = document.querySelectorAll('.disp_input');


sliders.forEach((slider, index) => {
    slider.oninput = function() {
        outputs[index].value = this.value;
    }
});
import Vue from 'vue';

const openClass = '-is-open';
const parentHasClass = (el, cls) => {
    while ((el = el.parentElement) && !el.classList.contains(cls));
    return el;
};

Vue.directive('click-outside', {
    bind: function (el, binding) {
        document.addEventListener('click', (event) => {
            const clickedInsideContainer = el.contains(event.target);

            /**
             * If the user clicked outside of the container and the directive target has the open clas
             * OR if the user clicked outside of the container and the directive target PARENT has the open class.
             */
            if (!clickedInsideContainer && el.classList.contains(openClass) || !clickedInsideContainer && parentHasClass(el, openClass)) {
                binding.value();
            }

            /**
             * If the user clicked inside of the container and the event happened on an .DropdownItem element
             */
            if (clickedInsideContainer && event.target.classList.contains('dropdown-item')) {
                binding.value();
            }
        });
    }
});
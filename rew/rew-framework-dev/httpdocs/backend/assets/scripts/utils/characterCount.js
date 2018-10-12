/**
 * Update character count for input elements
 * @param {string} input - The `<input>` element `id`
 * @param {string} inputCount - The character count container
 * @param {string} inputMax - The maximum character limit number container
 * @param {number} maxlength - The maximum number of characters allowed
 * @returns {undefined}
 */

export default (input, inputCount, inputMax, maxlength) => {
    let remainingCharacters;
    const textInput = document.getElementById(input);
    const count = document.getElementById(inputCount);
    const max = document.getElementById(inputMax);
    if (max) max.innerHTML = maxlength;
    if (count) count.innerHTML = maxlength - textInput.value.length;
    if (textInput) {
        textInput.addEventListener('keyup', () => {
            remainingCharacters = maxlength - textInput.value.length;
            if (remainingCharacters < 0) return;
            count.innerHTML = remainingCharacters;
        }, false);
    }
};

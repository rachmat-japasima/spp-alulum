import "./bootstrap";

import Alpine from "alpinejs";
import AutoNumeric from "autonumeric";

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    AutoNumeric.multiple(".money", {
        digitGroupSeparator: ".",
        decimalCharacter: ",",
        decimalPlaces: 0,
        minimumValue: "0",
        modifyValueOnWheel: false,
        unformatOnSubmit: true,
    });
});

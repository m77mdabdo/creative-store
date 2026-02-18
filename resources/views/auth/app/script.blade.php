
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap 4 -->
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input  = document.querySelector("#phone_input");
    const hidden = document.querySelector("#phone_e164");
    const form   = input?.closest("form");

    if (!input || !hidden || !form) return;

    if (!window.intlTelInput) {
        console.error("intlTelInput not loaded (check Vite: npm run dev/build).");
        return;
    }

    const iti = window.intlTelInput(input, {
        separateDialCode: true,
        nationalMode: true,
        preferredCountries: ["eg","sa","ae","kw","qa"],
        initialCountry: "eg",
        utilsScript: "/build/intl-tel-input-utils.js",
    });

    function buildE164Fallback() {
        const rawDigits = (input.value || "").replace(/\D/g, "");
        if (!rawDigits) return "";

        const dialCode = iti.getSelectedCountryData()?.dialCode;
        if (!dialCode) return rawDigits;

        return `+${dialCode}${rawDigits}`;
    }

    function syncPhone() {
        const raw = (input.value || "").trim();
        if (!raw) { hidden.value = ""; return; }

        // حاول تجيب رقم دولي من المكتبة
        const e164 = iti.getNumber && iti.getNumber();
        hidden.value = e164 && e164.startsWith("+") ? e164 : buildE164Fallback();

        console.log("PHONE HIDDEN:", hidden.value);
    }

    input.addEventListener("change", syncPhone);
    input.addEventListener("keyup", syncPhone);
    form.addEventListener("submit", syncPhone);
});
</script>





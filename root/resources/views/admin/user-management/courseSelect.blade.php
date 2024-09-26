<!-- Choices.jsのJS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('course');
        const choices = new Choices(selectElement, {
            removeItemButton: true,
            allowHTML: true
        });
        selectElement.addEventListener('change', function(event) {
            const selectedValues = choices.getValue(true);
            console.log(selectedValues);
        });
    });
</script>
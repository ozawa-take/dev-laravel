<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('user');
        const choices = new Choices(selectElement, {
            removeItemButton: true,
            allowHTML: true,
            shouldSort: false,
        });
        selectElement.addEventListener('change', function(event) {
            const selectedValues = choices.getValue(true);
            console.log(selectedValues);
        });
    });
</script>
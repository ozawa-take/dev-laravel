<!-- SortableJS -->
<script defer src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" integrity="sha384-eeLEhtwdMwD3X9y+8P3Cn7Idl/M+w8H4uZqkgD/2eJVkWIN1yKzEj6XegJ9dL3q0" crossorigin="anonymous"></script>

<script>

document.addEventListener("DOMContentLoaded", (event) => {
    const saveBtn = document.getElementById("saveBtn");
    const backBtn = document.getElementById("backBtn");

    // 並び替えの処理
    new Sortable(document.getElementById("tableBody"), {
        animation: 150,
        onEnd: function (event) {
            saveBtn.disabled = false;
            backBtn.disabled = false;
        }
    });

    // 元に戻すボタンを押した時の処理（リロード）
    backBtn.addEventListener("click", function() {
        backBtn.disabled = false;
        location.reload();
    })

    // 変更確定ボタンを押した時の処理
    saveBtn.addEventListener("click", function() {
        saveBtn.disabled = true;
        backBtn.disabled = true;
        saveSortOrder();
    });

    // 非同期処理
    async function saveSortOrder() {
        const rows = document.querySelectorAll("#sortable tbody tr");
        const positions = Array.from(rows).map(row => row.dataset.id);

        try {
            const response = await fetch("{{ route('admin.contents.sort') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ positions: positions })
            });

            const data = await response.json();
            console.log(data.message);

            // 並び替えが完了したことを表示
            const message = document.getElementById('message');
            const messageClass = message.classList.add("alert", "alert-success");
            message.innerText = data.message;
        } catch (error) {
            console.error(error);
        }
    }
});

</script>
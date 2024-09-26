<script>
document.addEventListener("DOMContentLoaded", (event) => {
    // 日付の文字列をDateオブジェクトに変換
    function parseDate(dateString) {
        const [year, month, day] = dateString.split("-");
        return new Date(year, month -1, day);
    }

    // テーブルのヘッダーに昇順、降順がわかるように三角を入れる
    function toggleSortIcon(header, direction) {
        const otherHeaders = document.querySelectorAll("#sortable th");
        otherHeaders.forEach((otherHeaders) => {
            if (otherHeaders !== header) {
                otherHeaders.textContent = otherHeaders.textContent.replace(/ ▲| ▼/g, "");
            }
        });
        const icon = direction === "asc" ? "▲" : "▼";
        header.textContent = header.textContent.replace(/ ▲| ▼/g, "") + " " + icon;
    }

    // テーブルヘッダーのクリックイベント
    document.querySelectorAll("#sortable th").forEach((header) => {
        header.addEventListener("click", () => {
            const table = document.getElementById("sortable");
            const columnIndex = Array.from(header.parentElement.children).indexOf(header);
            const isDateColumn = header.classList.contains("col-date");

            const rows = Array.from(table.querySelectorAll("tbody tr"));
            const sortedRows = [];

            // データを格納
            rows.forEach((row) => {
                const cell = row.children[columnIndex];
                const cellData = cell.textContent.trim();
                const rowData = isDateColumn ? parseDate(cellData) : cellData;

                sortedRows.push({ element: row, data:rowData});
            });

            const currentDirection = header.getAttribute("data-sort");

            // 並び替え
            if (currentDirection === "asc" || currentDirection === "desc") {
                sortedRows.sort((a, b) => {
                if(currentDirection === "asc") {
                    return a.data< b.data ? -1 : 1;
                } else {
                    return a.data > b.data ? -1 : 1;
                }
                });

                // 昇順・降順アイコンの切り替え
                header.setAttribute("data-sort", currentDirection === "asc" ? "desc" : "asc");
                toggleSortIcon(header, currentDirection);

                // データを再配置
                const tbody = table.querySelector("tbody");
                tbody.innerHTML = "";
                sortedRows.forEach((sortedRow) => {
                    tbody.appendChild(sortedRow.element);
                });

            }
        });
    });
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        document.getElementById('searchForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch("{{ route('admin.admin-management.search') }}", {
                    method: 'POST',
                    body: formData,
                });

                if (!response.ok) {
                    throw new Error('検索に失敗しました。');
                }
                const data = await response.json();
                const tbody = document.querySelector('#sortable tbody');
                tbody.innerHTML = '';

                data.forEach((result) => {
                    const row = document.createElement('tr');
                    let date = new Date(result.created_at);
                    let formatDate = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2) + " " + ("0" + date.getHours()).slice(-2) + ":" + ("0" + date.getMinutes()).slice(-2) + ":" + ("0" + date.getSeconds()).slice(-2);
                    row.innerHTML = `
                    <td class="align-middle">${result.username}</td>
                    <td class="align-middle text-center">${result.mail_address}</td>
                    <td class="align-middle text-center">
                        ${result.login_at ? result.login_at : ''}
                    </td>
                    <td class="align-middle text-center">${formatDate} </td>
                    <td class="text-center">
                    @can('is_system_admin',$adminUser)
                    ${!result.is_system_admin ? `
                <form action="admin-management/${result.id}" method="post" class="d-inline">
                    @csrf
                    @method('delete')
                    <input class="btn btn-danger" type="submit" value="削除"
                    onClick="return confirm('本当に削除しますか？');">
                </form>
            ` : ''}
                    @endcan
                    </td>

                `;
                    tbody.appendChild(row);
                });

            } catch (error) {
                console.error(error);
                alert('検索に失敗しました。');
            }
        });
    });
</script>
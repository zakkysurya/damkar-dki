function initDataTable({
    selector = '#data-table',
    url = '',
    columns = [],
    pageLength = 10,
    orderIndex = 0,
    orderSorter = 'desc',
    enableFilter = false, // Opsi untuk mengaktifkan filter
    filterSelector = null // Selector filter, jika ada
} = {}) {
    return $(selector).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            type: 'GET',
            data: function (d) {
                if (enableFilter && filterSelector) {
                    // Tambahkan parameter filter dari input jika filter diaktifkan
                    $(filterSelector).each(function () {
                        const name = $(this).attr('name');
                        const value = $(this).val();
                        if (name && value !== undefined) {
                            d[name] = value; // Tambahkan ke data
                        }
                    });
                }
            },
            error: function (xhr, error) {
                console.log({
                    xhr,
                    error
                });
                Swal.fire({
                    title: 'Gagal!',
                    text: `Error: ${xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data.'}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        columns: columns,
        pageLength: pageLength, // Jumlah data per halaman
        lengthMenu: [10, 25, 50, 100], // Opsi jumlah data per halaman
        order: [
            [orderIndex, orderSorter]
        ]
    });
}


document.addEventListener("DOMContentLoaded", function () {

    // MEMUAT DAFTAR GEDUNG
    const pilihanGedung = document.getElementById("gedung");

    if (pilihanGedung) {
        fetch("api/gedung.php")
            .then(async function (response) {
                const hasil = await response.json();

                if (!response.ok || hasil.status !== "success") {
                    throw new Error(hasil.pesan || "Gagal memuat gedung.");
                }

                return hasil.data;
            })
            .then(function (daftarGedung) {
                pilihanGedung.innerHTML =
                    '<option value="">Pilih gedung tempat barang hilang atau ditemukan</option>';

                daftarGedung.forEach(function (gedung) {
                    const opsi = document.createElement("option");

                    opsi.value = gedung.kode_gedung;
                    opsi.textContent = gedung.kode_gedung + " - " + gedung.nama_gedung;

                    pilihanGedung.appendChild(opsi);
                });
            })
            .catch(function (error) {
                console.error("Kesalahan daftar gedung:", error);

                pilihanGedung.innerHTML =
                    '<option value="">Daftar gedung gagal dimuat</option>';
            });
    }


    // MENGATUR PILIHAN JENIS LAPORAN
    const pilihanJenis = document.getElementById("jenis");
    const parameter = new URLSearchParams(window.location.search);
    const jenisDariUrl = parameter.get("jenis");

    if (pilihanJenis) {
        if (jenisDariUrl === "hilang" || jenisDariUrl === "temuan") {
            pilihanJenis.value = jenisDariUrl;
        }
    }


    // MENGIRIM FORM LAPORAN
    const formLaporan = document.getElementById("formLaporan");

    if (formLaporan) {
        formLaporan.addEventListener("submit", function (event) {
            event.preventDefault();

            const tombolKirim = formLaporan.querySelector(
                'button[type="submit"], input[type="submit"]'
            );

            const dataForm = new FormData(formLaporan);

            if (tombolKirim) {
                tombolKirim.disabled = true;
                tombolKirim.textContent = "Mengirim...";
            }

            fetch("api/simpan.php", {
                method: "POST",
                body: dataForm
            })
            .then(async function (response) {
                const teks = await response.text();

                let hasil;

                try {
                    hasil = JSON.parse(teks);
                } catch (error) {
                    console.error("Jawaban dari PHP:", teks);
                    throw new Error(
                        "PHP tidak mengirim JSON yang valid. Periksa api/simpan.php."
                    );
                }

                if (!response.ok || hasil.status !== "success") {
                    throw new Error(
                        hasil.pesan || "Laporan gagal dikirim."
                    );
                }

                return hasil;
            })
            .then(function (hasil) {
                alert(hasil.pesan || "Laporan berhasil dikirim!");

                formLaporan.reset();

                if (pilihanJenis && jenisDariUrl) {
                    pilihanJenis.value = jenisDariUrl;
                }

                if (pilihanGedung) {
                    pilihanGedung.value = "";
                }
            })
            .catch(function (error) {
                console.error(error);
                alert(error.message);
            })
            .finally(function () {
                if (tombolKirim) {
                    tombolKirim.disabled = false;
                    tombolKirim.textContent = "Kirim Laporan";
                }
            });
        });
    }

});

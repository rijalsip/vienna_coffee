<?php
include "proses/connect.php";

// Query untuk list order
$query = mysqli_query($conn, "SELECT *, SUM(harga*jumlah) AS harganya, tb_order.waktu_order FROM tb_list_order
  LEFT JOIN tb_order ON tb_order.id_order = tb_list_order.kode_order
  LEFT JOIN tb_daftar_menu ON tb_daftar_menu.id = tb_list_order.menu
  LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
  GROUP BY id_list_order 
  HAVING tb_list_order.kode_order = $_GET[order]
");

$kode = $_GET['order'];
$meja = $_GET['meja'];
$pelanggan = $_GET['pelanggan'];

while ($record = mysqli_fetch_array($query)) {
  $result[] = $record;
}

// ✅ Tambahkan ini:
$select_menu = mysqli_query($conn, "SELECT * FROM tb_daftar_menu");

$menu_options = array();
if ($select_menu) {
  while ($value = mysqli_fetch_assoc($select_menu)) {
    $menu_options[] = $value;
  }
} else {
  echo "Query menu gagal: " . mysqli_error($conn);
}

?>

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<div class="col-lg-9 mt-2">
  <div class="card">
    <div class="card-header">
      Halaman Order Item
    </div>
    <div class="card-body">
      <a href="order" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i></a>
      <div class="row">
        <div class="col-lg-3">
          <div class="form-floating mb-3">
            <input disabled type="text" class="form-control" id="kodeorder" value="<?php echo $kode; ?>">
            <label for="uploadfoto">Kode Order</label>
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-floating mb-3">
            <input disabled type="text" class="form-control" id="meja" value="<?php echo $meja; ?>">
            <label for="uploadfoto">Meja</label>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="form-floating mb-3">
            <input disabled type="text" class="form-control" id="pelanggan" value="<?php echo $pelanggan; ?>">
            <label for="uploadfoto">Pelanggan</label>
          </div>
        </div>
      </div>

      <!-- Modal Tambah Item Baru -->
      <div class="modal fade" id="tambahitem" tabindex="-1" aria-labelledby="ModalTambahUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="ModalTambahUserLabel">Tambah Menu Makanan dan Minuman</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form class="needs-validation" novalidate action="proses/proses_input_orderitem.php" method="POST">
                <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                <input type="hidden" name="meja" value="<?php echo $meja ?>">
                <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                <div class="row">
                  <div class="col-lg-8">
                    <div class="form-floating mb-3">
                      <select class="form-select" name="menu" id="">
                        <option selected hidden value="">Pilih Menu</option>
                        <?php
                        foreach ($menu_options as $value) {
                          echo "<option value='" . $value['id'] . "'>" . $value['nama_menu'] . "</option>";
                        }
                        ?>
                      </select>
                      <label for=menu">Menu Makanan/Minuman</label>
                      <div class="invalid-feedback">
                        Pilih Menu
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-floating">
                      <input type="Number" class="form-control" id="TUusername" placeholder="Jumlah Porsi" name="jumlah" required>
                      <label for="TUusername">Jumlah Porsi</label>
                      <div class="invalid-feedback">Masukan Jumlah Porsi.</div>
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-12">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="TUketerangan" placeholder="Masukkan catatan" name="catatan">
                      <label for="TUketerangan">catatan</label>
                    </div>
                  </div>
                </div>


                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  <button type="submit" class="btn btn-primary" name="input_orderitem_validate" value="12345">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Akhir Modal Tambah item Baru -->




      <?php
      if (empty($result)) {
        echo "Data menu makanan atau minuman tidak ada";
      } else {
        foreach ($result as $row) {
      ?>


          <!-- Modal Edit -->
          <div class="modal fade" id="ModalEdit<?php echo $row['id_list_order'] ?>"" tabindex=" -1" aria-labelledby="ModalTambahUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-fullscreen-md-down">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="ModalTambahUserLabel">Edit Menu Makanan dan Minuman</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form class="needs-validation" novalidate action="proses/proses_edit_orderitem.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id_list_order'] ?>">
                    <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                    <input type="hidden" name="meja" value="<?php echo $meja ?>">
                    <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                    <div class="row">
                      <div class="col-lg-8">
                        <div class="form-floating mb-3">
                          <select class="form-select" name="menu" id="">
                            <option selected hidden value="">Pilih Menu</option>
                            <?php
                            foreach ($menu_options as $value) {
                              if ($row['menu'] == $value['id']) {
                                echo "<option selected value='" . $value['id'] . "'>" . $value['nama_menu'] . "</option>";
                              } else {
                                echo "<option value='" . $value['id'] . "'>" . $value['nama_menu'] . "</option>";
                              }
                            }
                            ?>
                          </select>
                          <label for=menu">Menu Makanan/Minuman</label>
                          <div class="invalid-feedback">
                            Pilih Menu
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-floating">
                          <input type="Number" class="form-control" id="TUusername" placeholder="Jumlah Porsi" name="jumlah" required value="<?php echo $row['jumlah'] ?>">
                          <label for="TUusername">Jumlah Porsi</label>
                          <div class="invalid-feedback">Masukan Jumlah Porsi.</div>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <div class="col-12">
                        <div class="form-floating">
                          <input type="text" class="form-control" id="TUketerangan" placeholder="Masukkan catatan" name="catatan" value="<?php echo $row['catatan'] ?>">
                          <label for="TUketerangan">catatan</label>
                        </div>
                      </div>
                    </div>


                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-primary" name="edit_orderitem_validate" value="12345">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Akhir Modal Edit -->

          <!-- Modal Delete -->
          <div class="modal fade" id="ModalDelete<?php echo $row['id_list_order'] ?>" tabindex="-1" aria-labelledby="#ModalViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-fullscreen-md-down">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="ModalViewLabel">Hapus Menu</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form class="needs-validation" novalidate action="proses/proses_delete_orderitem.php" method="POST">
                  <input type="hidden" value="<?php echo $row['id_list_order'] ?>" name="id">
                  <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                  <input type="hidden" name="meja" value="<?php echo $meja ?>">
                  <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                  <div class="col-lg-12">
                    Apakah anda ingin menghapus menu <b> <?php echo $row['nama_menu'] ?> </b>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger" name="delete_orderitem_validate" value="12345">Hapus</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- Akhir Modal Delete -->




        <?php
        }
        ?>
        <!-- Modal Bayar Baru -->
        <div class="modal fade" id="bayar" tabindex="-1" aria-labelledby="ModalTambahUserLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-fullscreen-md-down">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="ModalTambahUserLabel">Pembayaran</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class=" table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr class="text-nowrap">
                        <th scope="col">Menu</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Status</th>
                        <th scope="col">Catatan</th>
                        <th scope="col">Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $total = 0;
                      foreach ($result as $row) {
                      ?>
                        <tr>
                          <td> <?php echo $row['nama_menu'] ?></td>
                          <td> <?php echo number_format($row['harga'], 0, ',', '.') ?></td>
                          <td> <?php echo $row['jumlah'] ?> </td>
                          <td> <?php echo $row['status'] ?> </td>
                          <td> <?php echo $row['catatan'] ?> </td>
                          <td>
                            <?php echo number_format($row['harganya'], 0, ',', '.') ?>
                          </td>

                        </tr>
                      <?php
                        $total += $row['harganya'];
                      }
                      ?>
                      <tr>
                        <td colspan="5" class="fw-bold">
                          Total harga
                        </td>
                        <td class="fw-bold">
                          <?php
                          echo number_format($total, 0, ',', '.')
                          ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <span class="text-danger fs-5 fw-semibold">Apakah anda yakin ingin melakukan Pembayaran?*</span>
                <form class="needs-validation" novalidate action="proses/proses_bayar.php" method="POST">
                  <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                  <input type="hidden" name="meja" value="<?php echo $meja ?>">
                  <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                  <input type="hidden" name="total" value="<?php echo $total ?>">
                  <div class="row">

                    <div class="col-lg-12">
                      <div class="form-floating">
                        <input type="n
                        umber" class="form-control" id="TUusername" placeholder="Nominal Uang" name="uang" required>
                        <label for="TUusername">Nominal Uang</label>
                        <div class="invalid-feedback">Masukan Nominal Uang</div>
                      </div>
                    </div>
                  </div>


                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" name="bayar_validate" value="12345">Bayar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Akhir Modal Bayar Baru -->



        <?php

        ?>
        <div class=" table-responsive">
          <table class="table table-hover">
            <thead>
              <tr class="text-nowrap">
                <th scope="col">Menu</th>
                <th scope="col">Harga</th>
                <th scope="col">Jumlah</th>
                <th scope="col">Status</th>
                <th scope="col">Catatan</th>
                <th scope="col">Total</th>
                <th scope="col">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              foreach ($result as $row) {
              ?>
                <tr>
                  <td> <?php echo $row['nama_menu'] ?></td>
                  <td> <?php echo number_format($row['harga'], 0, ',', '.') ?></td>
                  <td> <?php echo $row['jumlah'] ?> </td>
                  <td> <?php
                        if ($row['status'] == 1) {
                          echo "<span class='badge text-bg-warning'>Masuk ke dapur</span>";
                        } elseif ($row['status'] == 2) {
                          echo "<span class='badge text-bg-primary'>Siap Saji</span>";
                        }
                        ?> </td>
                  <td> <?php echo $row['catatan'] ?> </td>
                  <td>
                    <?php echo number_format($row['harganya'], 0, ',', '.') ?>
                  </td>
                  <td>
                    <div class="d-flex">
                      <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-1 disabled" : "btn btn-warning btn-sm me-1"; ?>" data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['id_list_order'] ?>" title="Edit Data"><i class="bi bi-pencil-square"></i></button>
                      <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-1 disabled" : "btn btn-danger btn-sm me-1"; ?>" data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['id_list_order'] ?>" title="Hapus Data"><i class="bi bi-trash"></i></button>
                    </div>
                  </td>
                </tr>
              <?php
                $total += $row['harganya'];
              }
              ?>
              <tr>
                <td colspan="5" class="fw-bold">
                  Total harga
                </td>
                <td class="fw-bold">
                  <?php
                  echo number_format($total, 0, ',', '.')
                  ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php } ?>
      <div>
        <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary disabled" : "btn btn-success"; ?>" data-bs-toggle="modal" data-bs-target="#tambahitem"><i class="bi bi-plus-circle"></i> Item</button>
        <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary disabled" : "btn btn-primary"; ?>" data-bs-toggle="modal" data-bs-target="#bayar"><i class="bi bi-cash-coin"></i> Bayar</button>
        <button onclick="printStruk()" class="btn btn-info"><i class="bi bi-printer"></i> Cetak Struk</button>
      </div>



    </div>
  </div>
</div>


<div id="strukContent" class="d-none">
  <style>
    #struk {
      font-family: "Arial", sans-serif;
      font-size: 12px;
      max-width: 300px;
      border: 1px solid #ccc;
      padding: 10px;
      width: 80mm;
    }

    #struk h2 {
      text-align: center;
      color: #333;
    }

    #struk p {
      margin: 5px 0;
    }

    #struk table {
      font-size: 12px;
      border-collapse: collapse;
      margin-top: 10px;
      width: 100%;
    }

    #struk th,
    #struk td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    #struk .total {
      font-weight: bold;
    }
  </style>
  <div id="struk">
    <h2>Struk Pembayaran Vienna Coffee</h2>
    <p>Kode Order: <?php echo $kode ?></p>
    <p>Meja: <?php echo $meja ?></p>
    <p>Pelanggan: <?php echo $pelanggan ?></p>
    <p>Waktu Order <?php echo date('d/m/Y H:i:s', strtotime($result[0]['waktu_order'])) ?></p>
    <table>
  </div>
  <th>
    <tr class="total">
      <th>Menu</th>
      <th>Harga</th>
      <th>Jumlah</th>
      <th>Total</th>
    </tr>
  </th>
  <tbody>
    <?php
    $total = 0;
    foreach ($result as $row) { ?>
      <tr>
        <td><?php echo $row['nama_menu'] ?></td>
        <td><?php echo number_format($row['harga'], 0, ',', '.') ?></td>
        <td><?php echo $row['jumlah'] ?></td>
        <td><?php echo number_format($row['harganya'], 0, ',', '.') ?></td>
      </tr>
    <?php
      $total += $row['harganya'];
    }  ?>
    <tr>
      <td colspan="3">Total Harga</td>
      <td><?php echo number_format($total, 0, ',', '.') ?></td>
    </tr>
  </tbody>
  </table>
</div>

<script>
  function printStruk() {
    var strukContent = document.getElementById("strukContent").innerHTML;

    var printFrame = document.createElement('iframe');
    printFrame.style.display = 'none';
    document.body.appendChild(printFrame);
    printFrame.contentDocument.write(strukContent);
    printFrame.contentWindow.print();
    document.body.removeChild(printFrame);
  }
</script>
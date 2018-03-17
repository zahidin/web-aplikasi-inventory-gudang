<br><br><br>
    <div class="container text-center" style="margin: 2em auto;">
    <h2 class="tex-center">Tabel Barang Keluar</h2>
    <a href="<?=base_url('report/barangKeluarManual')?>" style="margin-bottom:10px;float:left;" type="button" class="btn btn-danger" name="laporan_data"><i class="fa fa-file-text" aria-hidden="true"></i> Invoice Manual</a>
    <div class="tabel" style="margin-top:80px">
    <table class="table table-bordered table-striped" style="margin: 2em auto;" id="tabel_barangkeluar">
    <thead>
      <tr>
        <th>No</th>
        <th>ID_Transaksi</th>
        <th>Tanggal Masuk</th>
        <th>Tanggal Keluar</th>
        <th>Lokasi</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Satuan</th>
        <th>Jumlah</th>
        <th>Invoice</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php if(is_array($list_data)){ ?>
        <?php $no = 1;?>
        <?php foreach($list_data as $dd): ?>
          <td><?=$no?></td>
          <td><?=$dd->id_transaksi?></td>
          <td><?=$dd->tanggal_masuk?></td>
          <td><?=$dd->tanggal_keluar?></td>
          <td><?=$dd->lokasi?></td>
          <td><?=$dd->kode_barang?></td>
          <td><?=$dd->nama_barang?></td>
          <td><?=$dd->satuan?></td>
          <td><?=$dd->jumlah?></td>
          <td><a type="button" class="btn btn-danger btn-report"  href="<?=base_url('report/barangKeluar/'.$dd->id_transaksi.'/'.$dd->tanggal_keluar)?>" name="btn_report" style="margin:auto;"><i class="fa fa-file-text" aria-hidden="true"></i></a></td>
      </tr>
    <?php $no++; ?>
    <?php endforeach;?>
    <?php }else { ?>
          <td colspan="7" align="center"><strong>Data Kosong</strong></td>
    <?php } ?>
    </tbody>
  </table>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#tabel_barangkeluar').DataTable();
  });
</script>

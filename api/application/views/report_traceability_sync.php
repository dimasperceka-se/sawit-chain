<table>
  <tr>
    <td>No. Surat Jalan</td>
    <td>No. Faktur</td>
    <td>No. Batch</td>
    <td>No. Trasaksi</td>
    <td>Unit Pembelian</td>
    <td>Tgl. Batch</td>
    <td>Tgl. Transaksi</td>
    <td>ID Petani</td>
    <td>Nama Petani</td>
    <td>Kabupaten</td>
    <td>Kecamatan</td>
    <td>Desa</td>
    <td>ID Kelompok</td>
    <td>Nama Kelompok</td>
    <td>Berat Kotor(Kg)</td>
    <td>Kadar Air</td>
    <td>Jumlah Biji</td>
    <td>Sampah</td>
    <td>Harga Tanpa Potongan</td>
    <td>Harga Dgn Potongan</td>
    <td>Berat Bersih(Kg)</td>
    <td>Total</td>
  </tr>
  <?php
    
    /*
    array(25) { 
        ["BuyingUnit"]=> string(12) "40073 - Ulis" 
        ["SupplyBatchNumber"]=> string(11) "15000400001" 
        ["InvoiceNumber"]=> string(5) "11513" 
        ["BatchDate"]=> string(10) "2015-05-17" 
        ["DestPO"]=> string(14) "06142015_40073" 
        ["DateTransaction"]=> string(19) "2015-05-17 00:00:00" 
        ["FarmerID"]=> string(9) "731201421" 
        ["FarmerName"]=> string(7) "Nusrah " 
        ["Village"]=> string(6) "Timusu" 
        ["SubDistrict"]=> string(9) "Liliriaja" 
        ["District"]=> string(7) "Soppeng" 
        ["CPGid"]=> string(8) "73120053" 
        ["GroupName"]=> string(11) "Malla Paowe" 
        ["FakturNumber"]=> string(6) "005251" 
        ["Bruto"]=> string(5) "36.00" 
        ["Netto"]=> string(5) "36.00" 
        ["Moisture"]=> string(4) "0.00" 
        ["BeanCount"]=> string(4) "0.00" 
        ["Mouldy"]=> NULL 
        ["Waste"]=> string(4) "0.00" 
        ["Insect"]=> NULL 
        ["Slaty"]=> NULL 
        ["ContractPrice"]=> string(8) "30000.00" 
        ["NetPrice"]=> string(8) "30000.00" 
        ["TotalPayment"]=> string(10) "1080000.00" }																					
    */
    
    foreach($data as $key => $value) { ?>
        <tr>
            <td><?php echo $value['DestPO']; ?></td>
            <td><?php echo $value['FakturNumber']; ?></td>
            <td><?php echo $value['SupplyBatchNumber']; ?></td>
            <td><?php echo $value['InvoiceNumber']; ?></td>
            <td><?php echo $value['BuyingUnit']; ?></td>
            <td><?php echo $value['BatchDate']; ?></td>
            <td><?php echo $value['DateTransaction']; ?></td>
            <td><?php echo $value['FarmerID']; ?></td>
            <td><?php echo $value['FarmerName']; ?></td>
            <td><?php echo $value['District']; ?></td>
            <td><?php echo $value['SubDistrict']; ?></td>
            <td><?php echo $value['Village']; ?></td>
            <td><?php echo $value['CPGid']; ?></td>
            <td><?php echo $value['GroupName']; ?></td>
            <td><?php echo $value['Bruto']; ?></td>
            <td><?php echo $value['Moisture']; ?></td>
            <td><?php echo $value['BeanCount']; ?></td>
            <td><?php echo $value['Waste']; ?></td>
            <td><?php echo $value['ContractPrice']; ?></td>
            <td><?php echo $value['NetPrice']; ?></td>
            <td><?php echo $value['Netto']; ?></td>
            <td><?php echo $value['TotalPayment']; ?></td>
        </tr>
  <?php
    }
  ?>
</table>

<?php
$now        = new Datetime('now');
$birthdate  = new Datetime($farmer['Birthdate']);
$age        = $now->diff($birthdate);
// $productivity = $garden_size['Production']/$garden_size['GardenHaUnCertified'];
// if ($productivity < 500) {
//     $professionalism = 'Unprofessional';
// } elseif ($productivity >= 500 && $productivity <= 1000) {
//     $professionalism = 'Progressing';
// } elseif ($productivity > 1000) {
//     $professionalism = 'Professional';
// }
?>
<!--<page size="A4">-->

<div class="clearfix" id="page"><!-- column -->

   <div class="clearfix" style="width:0px;height:0px;"></div>

   <div class="clearfix colelem" id="pu81-6"><!-- group -->
      <div class="clearfix grpelem" id="u81-6"><!-- content -->
         <p id="u81-2">BENEFICIARY PROFILES</p>
         <p id="u81-4"><?php echo strtoupper(lang('Farmer Name')) ?> : <?php echo strtoupper($farmer['FarmerName']) ?> - <?php echo $farmer['FarmerID'] ?></p>
      </div>

      <div class="grpelem" id="u82"><!-- custom html -->
         <div id="map_canvas_<?php echo $farmer['FarmerID'] ?>" style="width: 100%; height: 110px; position: absolute !important;"></div>
      </div>

      <img width="111" class="rounded-corners grpelem" id="u84" src="<?php echo base_url().'images/Photo/'.$farmer['Photo'] ?>" style="" />
   </div>

   <br />

   <div class="clearfix colelem" id="u110-4"><!-- content -->
      <p><?php echo strtoupper(lang('Basic Data')) ?></p>
   </div>

   <div class="clearfix colelem" id="ppu101-4"><!-- group -->
      <div class="clearfix grpelem" id="pu101-4"><!-- column -->

         <div class="clearfix colelem" id="u101-4"><!-- content -->
            <p><?php echo strtoupper(lang('Farmer ID')); ?></p>
         </div>

         <div class="gradient rounded-corners clearfix colelem" id="u100"><!-- group -->
            <div class="clearfix grpelem" id="u102-4"><!-- content -->
               <p><?php echo $farmer['FarmerID'] ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u106-4"><!-- content -->
            <p><?php echo strtoupper(lang('Farmer Name')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u105"><!-- group -->
            <div class="clearfix grpelem" id="u104-4"><!-- content -->
               <p><?php echo strtoupper($farmer['FarmerName']) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u107-4"><!-- content -->
            <p><?php echo strtoupper(lang('Provinsi')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u108"><!-- group -->
            <div class="clearfix grpelem" id="u109-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Provinsi']) ?></p>
            </div>
         </div>

      </div>

      <div class="grpelem" id="u118"><!-- simple frame --></div>

      <div class="clearfix grpelem" id="pu112-4"><!-- column -->

         <div class="clearfix colelem" id="u112-4"><!-- content -->
            <p><?php echo strtoupper(lang('District')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u113"><!-- group -->
            <div class="clearfix grpelem" id="u111-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Kabupaten']) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u114-4"><!-- content -->
            <p><?php echo strtoupper(lang('Sub District')) ?></p>
         </div>
         <div class="gradient rounded-corners colelem" id="u115">
            <div class="clearfix grpelem" id="u111-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Kecamatan']) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u116-4"><!-- content -->
            <p><?php echo strtoupper(lang('Village')) ?></p>
         </div>
         <div class="gradient rounded-corners colelem" id="u115">
            <div class="clearfix grpelem" id="u111-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Desa']) ?></p>
            </div>
         </div>

      </div>

   </div>

   <div class="colelem" id="u129" data-mu-ie-matrix="progid:DXImageTransform.Microsoft.Matrix(M11=0,M12=1,M21=-1,M22=0,SizingMethod='auto expand')" data-mu-ie-matrix-dx="-285" data-mu-ie-matrix-dy="285"><!-- simple frame --></div>

   <div class="clearfix colelem" id="u138-4"><!-- content -->
      <p><?php echo strtoupper(lang('Membership Data')) ?></p>
   </div>

   <div class="clearfix colelem" id="u139-4"><!-- content -->
      <p><?php echo strtoupper(lang('Group Name')) ?></p>
   </div>
   <div class="gradient rounded-corners clearfix colelem" id="u141"><!-- group -->
      <div class="clearfix grpelem" id="u140-4"><!-- content -->
         <p><?php echo $farmer['CPGid'] ?> - <?php echo strtoupper($farmer['GroupName']) ?></p>
      </div>
   </div>

   <div class="clearfix colelem" id="u144-4"><!-- content -->
      <p><?php echo strtoupper(lang('Cooperatives Name')) ?></p>
   </div>
   <div class="gradient rounded-corners colelem" id="u143">
      <div class="clearfix grpelem" id="u140-4"><!-- content -->
         <p><?php echo strtoupper($farmer['CoopName']) ?></p>
      </div>
   </div>

   <div class="colelem" id="u145" data-mu-ie-matrix="progid:DXImageTransform.Microsoft.Matrix(M11=0,M12=1,M21=-1,M22=0,SizingMethod='auto expand')" data-mu-ie-matrix-dx="-285" data-mu-ie-matrix-dy="285"><!-- simple frame --></div>

   <div class="clearfix colelem" id="u148-4"><!-- content -->
      <p><?php echo strtoupper(lang('Additional Data')) ?></p>
   </div>

   <div class="clearfix colelem" id="ppppu149-4">
      <!-- group -->
      <div class="clearfix grpelem" id="pppu149-4">
         <!-- column -->
         <div class="clearfix colelem" id="ppu149-4">
            <!-- group -->
            <div class="clearfix grpelem" id="pu149-4">
               <!-- column -->
               <div class="clearfix colelem" id="u149-4">
                  <!-- content -->
                  <p><?php echo strtoupper(lang('Age')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u150">
                  <!-- group -->
                  <div class="clearfix grpelem" id="u155-4">
                     <!-- content -->
                     <p><?php echo $age->y ?> <?php echo lang('yrs old') ?></p>
                  </div>
               </div>
            </div>
            <div class="grpelem" id="u161">
               <!-- simple frame -->
            </div>
            <div class="clearfix grpelem" id="pu176-4">
               <!-- column -->
               <div class="clearfix colelem" id="u176-4">
                  <!-- content -->
                  <p><?php echo strtoupper(lang('Gender')) ?></p>
               </div>
               <div class="clearfix colelem" id="u169">
                  <!-- group -->
                  <div class="rounded-corners clearfix grpelem" id="u172">
                     <!-- group -->
                     <div class="rounded-corners clearfix grpelem" id="u171">
                     <?php if($farmer['Gender']=='1'){?>
                        <div class="rounded-corners grpelem" id="u170"></div>
                     <?php }else{?>
                        <div style="background-color: white;" class="rounded-corners grpelem" id="u170"></div>
                     <?php }?>
                     </div>
                  </div>
               </div>
               <div class="clearfix colelem" id="u167-4">
                  <!-- content -->
                  <p><?php echo strtoupper(lang('Male')) ?></p>
               </div>
               <div class="clearfix colelem" id="u173">
                  <!-- group -->
                  <div class="rounded-corners clearfix grpelem" id="u174">
                     <!-- group -->
                     <div class="rounded-corners grpelem" id="u175">
                        <?php if($farmer['Gender']=='2'){?>
                           <div class="rounded-corners grpelem" id="u170">
                              <!-- simple frame -->
                           </div>
                        <?php }else{?>
                           <div style="background-color: white;" class="rounded-corners grpelem" id="u170">
                              <!-- simple frame -->
                           </div>
                        <?php }?>
                     </div>
                  </div>
               </div>
               <div class="clearfix colelem" id="u168-4">
                  <!-- content -->
                  <p><?php echo strtoupper(lang('Female')) ?></p>
               </div>
            </div>
         </div>
         <div class="clearfix colelem" id="u154-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Phone Number')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u153">
            <!-- group -->
            <div class="clearfix grpelem" id="u152-4">
               <!-- content -->
               <p><?php echo $farmer['HandPhone'] ?></p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u178">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu157-4">
         <!-- column -->
         <div class="clearfix colelem" id="u157-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Latest Education')) ?></p>
         </div>
         <div class="gradient rounded-corners colelem" id="u156">
            <div class="clearfix grpelem" id="u152-4">
               <!-- content -->
               <p><?php
                  $education = '';
                  switch ($farmer['Education']) {
                     case '1': $education = lang('No Schooling') ;break;
                     case '2': $education = lang('Primary School Incomplete') ;break;
                     case '3': $education = lang('Primary School Completed') ;break;
                     case '4': $education = lang('Junior High School') ;break;
                     case '5': $education = lang('Senior High School / Vocational') ;break;
                     case '5': $education = lang('Tertiary Degree') ;break;
                  }
                  echo strtoupper($education);
               ?></p>
            </div>
         </div>
         <div class="clearfix colelem" id="ppu160-4">
            <!-- group -->
            <div class="clearfix grpelem" id="pu160-4">
               <!-- column -->
               <div class="clearfix colelem" id="u160-4">
                  <!-- content -->
                  <p><?php echo strtoupper(lang('Family Status')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u158">
                  <!-- group -->
                  <div class="clearfix grpelem" id="u159-4">
                     <!-- content -->
                     <p><?php echo !empty($family) ? strtoupper(lang('Have')) : strtoupper(lang('No')) ?></p>
                  </div>
               </div>
            </div>
            <div class="grpelem" id="u165">
               <!-- simple frame -->
            </div>
            <div class="clearfix grpelem" id="pu164-4">
               <!-- column -->
               <div class="clearfix colelem" id="u164-4">
                  <!-- content -->
                  <p><?php echo strtoupper(lang('Nr of Family Number')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u163">
                  <!-- group -->
                  <div class="clearfix grpelem" id="u162-4">
                     <!-- content -->
                     <p>
                     <?php
                        $count = 0;
                        if (!empty($family)) {
                           foreach ($family as $key => $value) {
                              if ($value['HubunganKeluarga'] == '2') {
                                 $count++;
                              }
                           }
                           echo $count;
                        }
                     ?>
                     </p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="colelem" id="u177" data-mu-ie-matrix="progid:DXImageTransform.Microsoft.Matrix(M11=0,M12=1,M21=-1,M22=0,SizingMethod='auto expand')" data-mu-ie-matrix-dx="-285" data-mu-ie-matrix-dy="285">
      <!-- simple frame -->
   </div>

   <div class="clearfix colelem" id="pu179-4">
      <!-- group -->
      <div class="clearfix grpelem" id="u179-4">
         <!-- content -->
         <p><?php echo strtoupper(lang('Crops Data')) ?></p>
      </div>
      <div class="clearfix grpelem" id="u182-4">
         <!-- content -->
         <p><?php echo strtoupper(lang('Nr of Cocoa Farms')) ?></p>
      </div>
   </div>
   <div class="gradient rounded-corners clearfix colelem" id="u181">
      <!-- group -->
      <div class="clearfix grpelem" id="u180-4">
         <!-- content -->
         <p><?php echo count($gardens) ?></p>
      </div>
   </div>
   <div class="clearfix colelem" id="ppu183-4">
      <!-- group -->
      <div class="clearfix grpelem" id="pu183-4">
         <!-- column -->
         <div class="clearfix colelem" id="u183-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Baseline')) ?></p>
         </div>
         <div class="clearfix colelem" id="u188-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Total Hectare')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u186">
            <!-- group -->
            <div class="clearfix grpelem" id="u213-4">
               <!-- content -->
               <p><?php echo $baseline['Hectare'] ?> Ha</p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u189">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu190-4">
         <!-- column -->
         <div class="clearfix colelem" id="u190-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Total Production')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u191">
            <!-- group -->
            <div class="clearfix grpelem" id="u214-4">
               <!-- content -->
               <p><?php echo $baseline['Production'] ?> Kg</p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u192">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu193-4">
         <!-- column -->
         <div class="clearfix colelem" id="u193-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Total Cacao Trees')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u194">
            <!-- group -->
            <div class="clearfix grpelem" id="u215-4">
               <!-- content -->
               <p><?php echo $baseline['Tree'] ?></p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u195">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu196-4">
         <!-- column -->
         <div class="clearfix colelem" id="u196-4">
            <!-- content -->
            <p><?php echo lang('Average Yield/Hectare') ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u197">
            <!-- group -->
            <div class="clearfix grpelem" id="u216-4">
               <!-- content -->
               <p><?php echo number_format($baseline['Production']/$baseline['Hectare'],0,',','.') ?></p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u209">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu210-4">
         <!-- column -->
         <div class="clearfix colelem" id="u210-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Postline')) ?></p>
         </div>
         <div class="clearfix colelem" id="u199-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Total Hectare')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u198">
            <!-- group -->
            <div class="clearfix grpelem" id="u217-4">
               <!-- content -->
               <p><?php echo $postline['Hectare'] ?> Ha</p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u200">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu201-4">
         <!-- column -->
         <div class="clearfix colelem" id="u201-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Total Production')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u202">
            <!-- group -->
            <div class="clearfix grpelem" id="u218-4">
               <!-- content -->
               <p><?php echo $postline['Production'] ?> Kg</p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u203">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu204-4">
         <!-- column -->
         <div class="clearfix colelem" id="u204-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Total Cacao Trees')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u205">
            <!-- group -->
            <div class="clearfix grpelem" id="u219-4">
               <!-- content -->
               <p><?php echo $postline['Tree'] ?></p>
            </div>
         </div>
      </div>
      <div class="grpelem" id="u206">
         <!-- simple frame -->
      </div>
      <div class="clearfix grpelem" id="pu208-4">
         <!-- column -->
         <div class="clearfix colelem" id="u208-4">
            <!-- content -->
            <p><?php echo strtoupper(lang('Average Yield/Hectare')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u207">
            <!-- group -->
            <div class="clearfix grpelem" id="u220-4">
               <!-- content -->
               <p><?php echo number_format($postline['Production']/$postline['Hectare'],0,',','.') ?></p>
            </div>
         </div>
      </div>
   </div>

   <div class="colelem" id="u211" data-mu-ie-matrix="progid:DXImageTransform.Microsoft.Matrix(M11=0,M12=1,M21=-1,M22=0,SizingMethod='auto expand')" data-mu-ie-matrix-dx="-285" data-mu-ie-matrix-dy="285">
      <!-- simple frame -->
   </div>

   <div class="clearfix colelem" id="u212-4" style="width:288px;">
      <!-- content -->
      <p><?php echo strtoupper(lang('List of Trainings Participated Data')) ?></p>
   </div>

   <!-- DATA TABEL DISINI -->
   <div class="clearfix colelem" id="u221">

   <table style="width:99%;" cellspacing="0" class="tabel_list_print">
      <tr>
         <th style="text-align: center; width: 50px;"><?php echo lang('Batch') ?></th>
         <th style="text-align: center; width: 265px;"><?php echo lang('Trainings') ?></th>
         <th style="text-align: center; width: 60px;"><?php echo lang('Start') ?></th>
         <th style="text-align: center; width: 60px;"><?php echo lang('End') ?></th>
         <th style="text-align: center; width: 50px;"><?php echo lang('Days') ?></th>
         <th style="text-align: center; width: 50px;"><?php echo lang('Type') ?></th>
      </tr>
      <?php if ($trainings): ?>
         <?php foreach ($trainings as $key => $training): ?>
            <tr>
               <td style="text-align: center;"><?php echo $training['BatchNumber'] ?></td>
               <td><?php echo $training['CpgTrainings'] ?></td>
               <td style="text-align: center;"><?php echo date('Y-m-d', strtotime($training['TrainingStart'])) ?></td>
               <td style="text-align: center;"><?php echo date('Y-m-d', strtotime($training['TrainingEnd'])) ?></td>
               <td style="text-align: center;"><?php echo $training['TrainingDays'] ?></td>
               <td style="text-align: center;"><?php echo $training['type'] ?></td>
            </tr>
         <?php endforeach ?>
      <?php endif ?>
   </table>

   </div>

   <div class="verticalspacer"></div>

</div>

<!--</page>-->

<?php if($countData != $increData){?>
   <div class="page-break"></div>
<?php }?>


<?php if (!empty($gardens)): ?>

<script type="text/javascript">
   var icon_path = '<?php echo base_url() ?>' + 'images/map/';
   $('#map_canvas_<?php echo $farmer['FarmerID'] ?>').gmap3({
        map: {
            options: {
                center: [<?php echo $gardens[0]['Latitude'] ?>, <?php echo $gardens[0]['Longitude'] ?>],
                zoom: 11,
                mapTypeControl: false,
                panControl: false,
                zoomControl: false,
                //scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                rotateControlOptions: false,
                overviewMapControl: false,
                OverviewMapControlOptions: false,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: true
            }
        },
        marker:{
            values:[
                <?php foreach ($gardens as $key => $garden): ?>
                {latLng:[<?php echo $garden['Latitude'] ?>, <?php echo $garden['Longitude'] ?>], options: {icon: icon_path + "farmer.png"}},
                <?php endforeach ?>
            ],
            options:{
                draggable: false
            }
        }
   });
</script>

<?php else: ?>
    <script type="text/javascript">
        var tpl = ""
        $('#map_canvas_<?php echo $farmer['FarmerID'] ?>').html('<div style="margin: 90px auto 0px; text-align: center; color: #aaaaaa;">No Map</div>');
    </script>
<?php endif ?>
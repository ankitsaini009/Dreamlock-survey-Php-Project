
<?php 

  include  '../include/header.php';
  include  '../include/navbar.php';
   

  ?>
  <?php
    // Fetch data for total projects completed in a single day
      $day_sql = "SELECT DATE(time_stamp) AS day, COUNT(*) AS total_projects 
      FROM surveylog 
      WHERE status = 'COMPLETE' 
      GROUP BY day";
      $day_result = $conn->query($day_sql);
      $day_data = [];
      while ($row = $day_result->fetch_assoc()) {
      $day_data[] = $row;
      }

      // Fetch data for total projects completed in a single week
      $week_sql = "SELECT YEAR(time_stamp) AS year, WEEK(time_stamp) AS week, COUNT(*) AS total_projects 
      FROM surveylog 
      WHERE status = 'COMPLETE' 
      GROUP BY year, week";
      $week_result = $conn->query($week_sql);
      $week_data = [];
      while ($row = $week_result->fetch_assoc()) {
      $week_data[] = $row;
      }

      // Fetch data for total projects completed in a single month
      $month_sql = "SELECT YEAR(time_stamp) AS year, MONTH(time_stamp) AS month, COUNT(*) AS total_projects 
        FROM surveylog 
        WHERE status = 'COMPLETE' 
        GROUP BY year, month";
      $month_result = $conn->query($month_sql);
      $month_data = [];
      while ($row = $month_result->fetch_assoc()) {
      $month_data[] = $row;
      }

      // Ensure all months are present
      $all_months = [];
      for ($i = 1; $i <= 12; $i++) {
      $all_months[sprintf("%02d", $i)] = 0;
      }
      foreach ($month_data as $data) {
      $all_months[sprintf("%02d", $data['month'])] = $data['total_projects'];
      }
      $month_data = [];
      foreach ($all_months as $month => $count) {
      $month_data[] = [
      'month' => $month,
      'total_projects' => $count
      ];
      }

    // Query to get count of projects per country
      $country_sql = "SELECT project_country AS country, COUNT(*) AS project_count
      FROM projectdetails
      GROUP BY project_country";
      $country_result = $conn->query($country_sql);

      $countries = [];
      $countryCounts = [];

      // Fetch data
      while ($row = $country_result->fetch_assoc()) {
        $countries[] = $row['country'];
        $countryCounts[] = $row['project_count'];
      }
  ?>
  <?php 
      // Fetch data for pie chart
      $sql1 = "SELECT supplier_name, COUNT(*) as count FROM suppliers GROUP BY supplier_name";
      $result1 = $conn->query($sql1 );

      $suppliers = [];
      $supplierCounts = [];

      if ($result1->num_rows > 0) {
          while ($row = $result1->fetch_assoc()) {
              $suppliers[] = $row['supplier_name'];
              $supplierCounts[] = $row['count'];
          }
      } else {
          echo "0 results";
      }

  ?>

  <style> 
    .table_wrapper{display: block;max-height: 80vh; overflow-x: auto;overflow-y: auto;white-space: nowrap;}
    .table_feild{"width:15px;font-size:12px;"}
  </style>

<div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-home"></i>
        </span> Dashboard
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item active" aria-current="page">
            <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
          </li>
        </ul>
      </nav>
    </div>
<!-- ============================================================================================================================================================================== -->
    <div class="row">
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
          <div class="card-body">
            <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">Total Project <i class="mdi mdi-chart-line mdi-24px float-end"></i></h4>
            <?php  
              $all_project_query = "SELECT * from projectdetails";
              $all_project_query_run = mysqli_query($conn,$all_project_query);
              if($total_project = mysqli_num_rows($all_project_query_run)){
                echo '<h1> '.$total_project .' </h1>';
              }else{
                echo "<p> 0 </p>";
              }
              ?>                                       
          </div>
        </div>
      </div>
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
          <div class="card-body">
            <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">Active<i class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h4>
            <?php  
              $all_project_Active_query = "SELECT * from projectdetails WHERE status = 'Active'"  ;
              $all_project_Active_query_run = mysqli_query($conn,$all_project_Active_query);
              if($total_project_Active = mysqli_num_rows($all_project_Active_query_run)){            
                echo '<h1> '.$total_project_Active .' </h1>';
              }else{
                echo "<p> 0 </p>";
              }
            ?>                  
          </div>
        </div>
      </div>
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
          <div class="card-body">
            <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">Archived Projects<i class="mdi mdi-diamond mdi-24px float-end"></i>
            <?php  
              $all_project_archived_query = "SELECT * from projectdetails WHERE status = 'Archived'"  ;
              $all_project_query_run = mysqli_query($conn,$all_project_archived_query);
              if($total_project_archived = mysqli_num_rows($all_project_query_run)){
                echo '<h1> '.$total_project_archived .' </h1>';
              }else{
                echo "<p> 0 </p>";
              }?>
            </h4>                  
          </div>
        </div>
      </div>
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
          <div class="card-body">
            <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
            <h4 > Closed Project </h4>
            <?php  
              $all_project_Closed_query = "SELECT * from projectdetails WHERE status = 'Closed'"  ;
              $all_project_Closed_query_run = mysqli_query($conn,$all_project_Closed_query);
              if($total_project_Closed = mysqli_num_rows($all_project_Closed_query_run)){
                echo '<h1> '.$total_project_Closed .' </h1>';
              }else{
                echo "<p> 0 </p>";
              }
              ?>
          </div>
        </div>
      </div>          
    </div>
<!-- ============================================================================================================================================================================== -->
    <div class="row">
      <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="clearfix">
              <h4 class="card-title float-start">Project's Completed in :</h4>
              <ul class="flex">
                <li class="mx-2 hover:text-gray-600 cursor-pointer text-gray-400" id="day">
                  <button onclick="showChart('day')">Day</button>
                </li>
                <li class="text-gray-400">|</li>
                <li class="mx-2 hover:text-gray-600 cursor-pointer text-gray-400" id="week">
                  <button onclick="showChart('week')">Week</button>
                  </li>
                <li class="text-gray-400">|</li>
                <li class="mx-2 hover:text-gray-600 cursor-pointer text-gray-400" id="month">
                  <button onclick="showChart('month')">Month</button>
                </li>
              </ul>
            </div>
            <div>
              <canvas id="projectChart"></canvas>
            </div>
            <div class="mt-4">
              <h5 class="ml-6">Countries :</h5>
              <div class="flex justify-between mt-4 flex-wrap">
                <?php 
                for ($i=0; $i < count( $countries); $i++) { 
                  echo "<p>".$countries[$i]."</p>";
                }
                ?>
            </div>
          </div>
          </div>
        </div>
      </div>   
      <div class="col-md-5 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Suppliers :</h4>
              <div> 
                <canvas id="pieChart"></canvas>
              </div>
            </div>
          </div>
      </div>
    </div>
<!-- ============================================================================================================================================================================== -->
    <div class="row">
      <div class="col-md-3 stretch-card grid-margin cursor-pointer" onclick="handleClickOpen('Active')">
        <div class="card bg-gradient-primary card-img-holder text-white">
          <div class="card-body hover:bg-purple-400" >
              <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
              <h4 >Active<i class="mdi mdi-diamond mdi-24px float-end"></i> </h4>
              <?php  
                $sql = "SELECT COUNT(*) AS active_count FROM ProjectDetails WHERE status = 'active'";
                $result = $conn->query($sql);
                if ($result) {
                  $row = $result->fetch_assoc();
                  $cnt = $row['active_count'];
                  echo '<h1> '.$cnt .' </h1>';
                }
              else{
                  echo "<p> 0 </p>";
                }
              ?>
          </div>
        </div>
      </div>          
      <div class="col-md-3 stretch-card grid-margin cursor-pointer" onclick="handleClickOpen('InActive')">
        <div class="card bg-gradient-info card-img-holder text-white">
          <div class="card-body  hover:bg-blue-400">
            <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">InActive<i class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h4>
            <?php  
                  $sql = "SELECT COUNT(*) AS inActive_count FROM ProjectDetails WHERE status = 'InActive'";
                  $result = $conn->query($sql);
                  if ($result) {
                    $row = $result->fetch_assoc();
                    $cnt = $row['inActive_count'];
                    echo '<h1> '.$cnt .' </h1>';
                  }
                else{
                    echo "<p> 0 </p>";
                  }
              ?>                  
            </div>
          </div>
      </div>
      <div class="col-md-3 stretch-card grid-margin cursor-pointer" onclick="handleClickOpen('Archived')">
        <div class="card bg-gradient-success card-img-holder text-white">
          <div class="card-body hover:bg-green-400">
            <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">Archived Projects<i class="mdi mdi-diamond mdi-24px float-end"></i>
            <?php  
            $sql = "SELECT COUNT(*) AS archived_count FROM ProjectDetails WHERE status = 'Archived'";
            $result = $conn->query($sql);
            if ($result) {
              $row = $result->fetch_assoc();
              $cnt = $row['archived_count'];
              echo '<h1> '.$cnt .' </h1>';
            }
            else{
              echo "<p> 0 </p>";
            }  
            ?> 
            </h4>                  
          </div>
        </div>
      </div>
      <div class="col-md-3 stretch-card grid-margin cursor-pointer" onclick="handleClickOpen('Invoiced')">
        <div class="card bg-gradient-danger card-img-holder text-white">
          <div class="card-body hover:bg-green-400">
            <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">Invoiced <i class="mdi mdi-chart-line mdi-24px float-end"></i></h4>
            <?php  
            $sql = "SELECT COUNT(*) AS invoiced_count FROM ProjectDetails WHERE status = 'Invoiced'";
            $result = $conn->query($sql);
            if ($result) {
              $row = $result->fetch_assoc();
              $cnt = $row['invoiced_count'];
              echo '<h1> '.$cnt .' </h1>';
            }
            else{
              echo "<p> 0 </p>";
            }
              ?>                                       
          </div>
        </div>
      </div>
    </div>

<!-- ============================================================================================================================================================================== -->
  <div>
    <div class="col-lg-12 grid-margin stretch-card z-1" style="display:none;" id="Active-card">
      <div class="card">
        <div class="card-body">
        <div class="flex justify-between mt-2">
          <h4 class="card-title bg-white px-4 py-2 rounded-lg text-2xl font-bold"><?php echo "Active";?> projects</h4>
          <div class="cursor-pointer" id="closeBtn" onclick="handleClickClose('Active')">
            <h4  class="border-2 border-gray-600 px-2 py-1 rounded-full bg-white text-black ">X</h4>
          </div>
        </div>
            <?php
              $sql = "SELECT * FROM ProjectDetails WHERE Status = 'Active'";
              $result = $conn->query($sql); 
            ?>
            <table class="table table-hover table_wrapper">
              <thead>
                <tr class="shadow-sm sticky top-0">
                  <th class="text-wrap table-feild" >Project Name</th>
                  <th class="text-wrap table-feild" >Project Code</th>
                  <th class="text-wrap table-feild" >Client Name</th>
                  <th class="text-wrap table-feild" >Country</th>
                  <th class="text-wrap table-feild" >Manager</th>
                  <th class="text-wrap table-feild" >PreScreen</th>
                  <th class="text-wrap table-feild" >Complete</th>
                  <th class="text-wrap table-feild" >Terminate</th>
                  <th class="text-wrap table-feild" >QF</th>
                  <th class="text-wrap table-feild" >Drop</th>
                  <th class="text-wrap table-feild" >F.LOI</th>
                  <th class="text-wrap table-feild" >Avg.IR</th>
                  <th class="text-wrap table-feild" >CNV</th>
                  <th class="text-wrap table-feild" >Drop%</th>
                  <th class="text-wrap table-feild" >Last Complete</th>
                  <th class="text-wrap table-feild" >Remaining Completes</th>
                  <th class="text-wrap table-feild" >CPI</th>
                  <th class="text-wrap table-feild text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                                $filters = $row['filters'];
                                $prescreen = 'No';

                                // Check if filters contain "PreScreen"
                                if (strpos($filters, 'PreScreen') !== false) {
                                    $prescreen = 'Yes';
                                }
                                
                                // Get completes, terminates, and QF for the current project
                                $project_code = $row['project_code'];
                                $completes_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                $terminates_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Terminate'";
                                $qf_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'QF'";
                                $last_complete_sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                
                                $completes_result = $conn->query($completes_sql);
                                $terminates_result = $conn->query($terminates_sql);
                                $qf_result = $conn->query($qf_sql);
                                $last_complete_result = $conn->query($last_complete_sql);
                                
                                $completes = $completes_result->fetch_assoc()['count'];
                                $terminates = $terminates_result->fetch_assoc()['count'];
                                $qf = $qf_result->fetch_assoc()['count'];
                                $last_complete = $last_complete_result->fetch_assoc()['last_complete'];
                                
                                // Calculate IR (Incidence Rate) and CNV (Conversion Rate)
                                $total_hits = $completes + $terminates + $qf;
                                $ir = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                $cnv = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                
                                // Drop% placeholder (assuming you have logic to calculate these)
                                $drop_percentage = 0; // Placeholder for Drop%
                                
                                echo '<tr>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_name"]) . '</td>';
                                echo '<td class="text-wrap table-feild"><a href="./project_details.php?project_code=' . htmlspecialchars($project_code) . '" class="text-blue-500 hover:underline">' . htmlspecialchars($project_code) . '</a></td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["client"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_country"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_manager"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($prescreen) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($terminates) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($qf) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["LOI"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($ir, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($cnv, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($last_complete) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["sample_size"] - $completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["CPI"]) . '</td>';
                                echo '<td> 
                                          <div class="button-container flex ">
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Edit</button>
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Delete</button>
                                          </div>
                                      </td>';
                                echo '</tr>';
                                
                            }
                    }else{
                      echo "<p class='text-center text-red-500'>No results found.</p>";
                    }
                  ?>
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <div class="col-lg-12 grid-margin stretch-card z-1" style="display:none;" id="InActive-card">
      <div class="card">
        <div class="card-body">
        <div class="flex justify-between mt-2">
          <h4 class="card-title bg-white px-4 py-2 rounded-lg text-2xl font-bold"><?php echo "InActive";?> projects</h4>
          <div class="cursor-pointer" id="closeBtn" onclick="handleClickClose('InActive')">
            <h4  class="border-2 border-gray-600 px-2 py-1 rounded-full bg-white text-black ">X</h4>
          </div>
        </div>
            <?php
              $sql = "SELECT * FROM ProjectDetails WHERE Status = 'InActive'";
              $result = $conn->query($sql); 
            ?>
            <table class="table table-hover table_wrapper">
              <thead>
                <tr class="shadow-sm sticky top-0">
                  <th class="text-wrap table-feild" >Project Name</th>
                  <th class="text-wrap table-feild" >Project Code</th>
                  <th class="text-wrap table-feild" >Client Name</th>
                  <th class="text-wrap table-feild" >Country</th>
                  <th class="text-wrap table-feild" >Manager</th>
                  <th class="text-wrap table-feild" >PreScreen</th>
                  <th class="text-wrap table-feild" >Complete</th>
                  <th class="text-wrap table-feild" >Terminate</th>
                  <th class="text-wrap table-feild" >QF</th>
                  <th class="text-wrap table-feild" >Drop</th>
                  <th class="text-wrap table-feild" >F.LOI</th>
                  <th class="text-wrap table-feild" >Avg.IR</th>
                  <th class="text-wrap table-feild" >CNV</th>
                  <th class="text-wrap table-feild" >Drop%</th>
                  <th class="text-wrap table-feild" >Last Complete</th>
                  <th class="text-wrap table-feild" >Remaining Completes</th>
                  <th class="text-wrap table-feild" >CPI</th>
                  <th class="text-wrap table-feild text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                                $filters = $row['filters'];
                                $prescreen = 'No';

                                // Check if filters contain "PreScreen"
                                if (strpos($filters, 'PreScreen') !== false) {
                                    $prescreen = 'Yes';
                                }
                                
                                // Get completes, terminates, and QF for the current project
                                $project_code = $row['project_code'];
                                $completes_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                $terminates_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Terminate'";
                                $qf_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'QF'";
                                $last_complete_sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                
                                $completes_result = $conn->query($completes_sql);
                                $terminates_result = $conn->query($terminates_sql);
                                $qf_result = $conn->query($qf_sql);
                                $last_complete_result = $conn->query($last_complete_sql);
                                
                                $completes = $completes_result->fetch_assoc()['count'];
                                $terminates = $terminates_result->fetch_assoc()['count'];
                                $qf = $qf_result->fetch_assoc()['count'];
                                $last_complete = $last_complete_result->fetch_assoc()['last_complete'];
                                
                                // Calculate IR (Incidence Rate) and CNV (Conversion Rate)
                                $total_hits = $completes + $terminates + $qf;
                                $ir = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                $cnv = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                
                                // Drop% placeholder (assuming you have logic to calculate these)
                                $drop_percentage = 0; // Placeholder for Drop%
                                
                                echo '<tr>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_name"]) . '</td>';
                                echo '<td class="text-wrap table-feild"><a href="./project_details.php?project_code=' . htmlspecialchars($project_code) . '" class="text-blue-500 hover:underline">' . htmlspecialchars($project_code) . '</a></td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["client"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_country"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_manager"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($prescreen) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($terminates) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($qf) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["LOI"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($ir, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($cnv, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($last_complete) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["sample_size"] - $completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["CPI"]) . '</td>';
                                echo '<td> 
                                          <div class="button-container flex ">
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Edit</button>
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Delete</button>
                                          </div>
                                      </td>';
                                echo '</tr>';
                                
                            }
                    }else{
                      echo "<p class='text-center text-red-500'>No results found.</p>";
                    }
                  ?>
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <div class="col-lg-12 grid-margin stretch-card z-1" style="display:none;" id="Archived-card">
      <div class="card">
        <div class="card-body">
        <div class="flex justify-between mt-2">
          <h4 class="card-title bg-white px-4 py-2 rounded-lg text-2xl font-bold"><?php echo "Archived";?> projects</h4>
          <div class="cursor-pointer" id="closeBtn" onclick="handleClickClose('Archived')">
            <h4  class="border-2 border-gray-600 px-2 py-1 rounded-full bg-white text-black ">X</h4>
          </div>
        </div>
            <?php
              $sql = "SELECT * FROM ProjectDetails WHERE Status = 'Archived'";
              $result = $conn->query($sql); 
            ?>
            <table class="table table-hover table_wrapper">
              <thead>
                <tr class="shadow-sm sticky top-0">
                  <th class="text-wrap table-feild" >Project Name</th>
                  <th class="text-wrap table-feild" >Project Code</th>
                  <th class="text-wrap table-feild" >Client Name</th>
                  <th class="text-wrap table-feild" >Country</th>
                  <th class="text-wrap table-feild" >Manager</th>
                  <th class="text-wrap table-feild" >PreScreen</th>
                  <th class="text-wrap table-feild" >Complete</th>
                  <th class="text-wrap table-feild" >Terminate</th>
                  <th class="text-wrap table-feild" >QF</th>
                  <th class="text-wrap table-feild" >Drop</th>
                  <th class="text-wrap table-feild" >F.LOI</th>
                  <th class="text-wrap table-feild" >Avg.IR</th>
                  <th class="text-wrap table-feild" >CNV</th>
                  <th class="text-wrap table-feild" >Drop%</th>
                  <th class="text-wrap table-feild" >Last Complete</th>
                  <th class="text-wrap table-feild" >Remaining Completes</th>
                  <th class="text-wrap table-feild" >CPI</th>
                  <th class="text-wrap table-feild text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                                $filters = $row['filters'];
                                $prescreen = 'No';

                                // Check if filters contain "PreScreen"
                                if (strpos($filters, 'PreScreen') !== false) {
                                    $prescreen = 'Yes';
                                }
                                
                                // Get completes, terminates, and QF for the current project
                                $project_code = $row['project_code'];
                                $completes_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                $terminates_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Terminate'";
                                $qf_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'QF'";
                                $last_complete_sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                
                                $completes_result = $conn->query($completes_sql);
                                $terminates_result = $conn->query($terminates_sql);
                                $qf_result = $conn->query($qf_sql);
                                $last_complete_result = $conn->query($last_complete_sql);
                                
                                $completes = $completes_result->fetch_assoc()['count'];
                                $terminates = $terminates_result->fetch_assoc()['count'];
                                $qf = $qf_result->fetch_assoc()['count'];
                                $last_complete = $last_complete_result->fetch_assoc()['last_complete'];
                                
                                // Calculate IR (Incidence Rate) and CNV (Conversion Rate)
                                $total_hits = $completes + $terminates + $qf;
                                $ir = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                $cnv = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                
                                // Drop% placeholder (assuming you have logic to calculate these)
                                $drop_percentage = 0; // Placeholder for Drop%
                                
                                echo '<tr>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_name"]) . '</td>';
                                echo '<td class="text-wrap table-feild"><a href="./project_details.php?project_code=' . htmlspecialchars($project_code) . '" class="text-blue-500 hover:underline">' . htmlspecialchars($project_code) . '</a></td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["client"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_country"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_manager"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($prescreen) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($terminates) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($qf) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["LOI"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($ir, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($cnv, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($last_complete) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["sample_size"] - $completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["CPI"]) . '</td>';
                                echo '<td> 
                                          <div class="button-container flex ">
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Edit</button>
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Delete</button>
                                          </div>
                                      </td>';
                                echo '</tr>';
                                
                            }
                    }else{
                      echo "<p class='text-center text-red-500'>No results found.</p>";
                    }
                  ?>
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <div class="col-lg-12 grid-margin stretch-card z-1" style="display:none;" id="Invoiced-card">
      <div class="card">
        <div class="card-body">
        <div class="flex justify-between mt-2">
          <h4 class="card-title bg-white px-4 py-2 rounded-lg text-2xl font-bold"><?php echo "Invoiced";?> projects</h4>
          <div class="cursor-pointer" id="closeBtn" onclick="handleClickClose('Invoiced')">
            <h4  class="border-2 border-gray-600 px-2 py-1 rounded-full bg-white text-black ">X</h4>
          </div>
        </div>
            <?php
              $sql = "SELECT * FROM ProjectDetails WHERE Status = 'Invoiced'";
              $result = $conn->query($sql); 
            ?>
            <table class="table table-hover table_wrapper">
              <thead>
                <tr class="shadow-sm sticky top-0">
                  <th class="text-wrap table-feild" >Project Name</th>
                  <th class="text-wrap table-feild" >Project Code</th>
                  <th class="text-wrap table-feild" >Client Name</th>
                  <th class="text-wrap table-feild" >Country</th>
                  <th class="text-wrap table-feild" >Manager</th>
                  <th class="text-wrap table-feild" >PreScreen</th>
                  <th class="text-wrap table-feild" >Complete</th>
                  <th class="text-wrap table-feild" >Terminate</th>
                  <th class="text-wrap table-feild" >QF</th>
                  <th class="text-wrap table-feild" >Drop</th>
                  <th class="text-wrap table-feild" >F.LOI</th>
                  <th class="text-wrap table-feild" >Avg.IR</th>
                  <th class="text-wrap table-feild" >CNV</th>
                  <th class="text-wrap table-feild" >Drop%</th>
                  <th class="text-wrap table-feild" >Last Complete</th>
                  <th class="text-wrap table-feild" >Remaining Completes</th>
                  <th class="text-wrap table-feild" >CPI</th>
                  <th class="text-wrap table-feild text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                                $filters = $row['filters'];
                                $prescreen = 'No';

                                // Check if filters contain "PreScreen"
                                if (strpos($filters, 'PreScreen') !== false) {
                                    $prescreen = 'Yes';
                                }
                                
                                // Get completes, terminates, and QF for the current project
                                $project_code = $row['project_code'];
                                $completes_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                $terminates_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Terminate'";
                                $qf_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'QF'";
                                $last_complete_sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                
                                $completes_result = $conn->query($completes_sql);
                                $terminates_result = $conn->query($terminates_sql);
                                $qf_result = $conn->query($qf_sql);
                                $last_complete_result = $conn->query($last_complete_sql);
                                
                                $completes = $completes_result->fetch_assoc()['count'];
                                $terminates = $terminates_result->fetch_assoc()['count'];
                                $qf = $qf_result->fetch_assoc()['count'];
                                $last_complete = $last_complete_result->fetch_assoc()['last_complete'];
                                
                                // Calculate IR (Incidence Rate) and CNV (Conversion Rate)
                                $total_hits = $completes + $terminates + $qf;
                                $ir = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                $cnv = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                
                                // Drop% placeholder (assuming you have logic to calculate these)
                                $drop_percentage = 0; // Placeholder for Drop%
                                
                                echo '<tr>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_name"]) . '</td>';
                                echo '<td class="text-wrap table-feild"><a href="./project_details.php?project_code=' . htmlspecialchars($project_code) . '" class="text-blue-500 hover:underline">' . htmlspecialchars($project_code) . '</a></td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["client"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_country"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_manager"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($prescreen) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($terminates) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($qf) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["LOI"]) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($ir, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($cnv, 2) . '%') . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($last_complete) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["sample_size"] - $completes) . '</td>';
                                echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["CPI"]) . '</td>';
                                echo '<td> 
                                          <div class="button-container flex ">
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Edit</button>
                                              <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Delete</button>
                                          </div>
                                      </td>';
                                echo '</tr>';
                                
                            }
                    }else{
                      echo "<p class='text-center text-red-500'>No results found.</p>";
                    }
                  ?>
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <div class="col-lg-12 grid-margin stretch-card" id="Project-Details">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Project Details</h4>
            <?php
              $sql = "SELECT * FROM ProjectDetails";
              $result = $conn->query($sql); 
            ?>
              <table class="table table-hover table_wrapper" >
                <thead>
                  <tr class="shadow-sm sticky top-0">
                    <th class="text-wrap table-feild" >Project Name</th>
                    <th class="text-wrap table-feild" >Project Code</th>
                    <th class="text-wrap table-feild" >Client Name</th>
                    <th class="text-wrap table-feild" >Country</th>
                    <th class="text-wrap table-feild" >Manager</th>
                    <th class="text-wrap table-feild" >PreScreen</th>
                    <th class="text-wrap table-feild" >Complete</th>
                    <th class="text-wrap table-feild" >Terminate</th>
                    <th class="text-wrap table-feild" >QF</th>
                    <th class="text-wrap table-feild" >Drop</th>
                    <th class="text-wrap table-feild" >F.LOI</th>
                    <th class="text-wrap table-feild" >Avg.IR</th>
                    <th class="text-wrap table-feild" >CNV</th>
                    <th class="text-wrap table-feild" >Drop%</th>
                    <th class="text-wrap table-feild" >Last Complete</th>
                    <th class="text-wrap table-feild" >Remaining Completes</th>
                    <th class="text-wrap table-feild" >CPI</th>
                    <th class="text-wrap table-feild text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                      if ($result->num_rows > 0) {
                          while($row = $result->fetch_assoc()) {
                                  $filters = $row['filters'];
                                  $prescreen = 'No';
  
                                  // Check if filters contain "PreScreen"
                                  if (strpos($filters, 'PreScreen') !== false) {
                                      $prescreen = 'Yes';
                                  }
                                  
                                  // Get completes, terminates, and QF for the current project
                                  $project_code = $row['project_code'];
                                  $completes_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                  $terminates_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Terminate'";
                                  $qf_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'QF'";
                                  $last_complete_sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                                  
                                  $completes_result = $conn->query($completes_sql);
                                  $terminates_result = $conn->query($terminates_sql);
                                  $qf_result = $conn->query($qf_sql);
                                  $last_complete_result = $conn->query($last_complete_sql);
                                  
                                  $completes = $completes_result->fetch_assoc()['count'];
                                  $terminates = $terminates_result->fetch_assoc()['count'];
                                  $qf = $qf_result->fetch_assoc()['count'];
                                  $last_complete = $last_complete_result->fetch_assoc()['last_complete'];
                                  
                                  // Calculate IR (Incidence Rate) and CNV (Conversion Rate)
                                  $total_hits = $completes + $terminates + $qf;
                                  $ir = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                  $cnv = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                                  
                                  // Drop% placeholder (assuming you have logic to calculate these)
                                  $drop_percentage = 0; // Placeholder for Drop%
                                  
                                  echo '<tr>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_name"]) . '</td>';
                                  echo '<td class="text-wrap table-feild"><a href="./project_details.php?project_code=' . htmlspecialchars($project_code) . '" class="text-blue-500 hover:underline">' . htmlspecialchars($project_code) . '</a></td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["client"]) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_country"]) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["project_manager"]) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($prescreen) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($completes) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($terminates) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($qf) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["LOI"]) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($ir, 2) . '%') . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars(number_format($cnv, 2) . '%') . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($drop_percentage) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($last_complete) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["sample_size"] - $completes) . '</td>';
                                  echo '<td class="text-wrap table-feild">' . htmlspecialchars($row["CPI"]) . '</td>';
                                  echo '<td> 
                                            <div class="button-container flex ">
                                                <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Edit</button>
                                                <button class="border-black m-1 py-1 border-2 rounded-lg  px-3 ">Delete</button>
                                            </div>
                                        </td>';
                                  echo '</tr>';
                                  
                              }
                      }else{
                        echo "<p class='text-center text-red-500'>No results found.</p>";
                      }
                      $conn->close();
                    ?>
                    </tbody>
              </table>
            </div>
        </div>
    </div>
  </div>
</div>
<!-- ============================================================================================================================================================================== -->

  <script>
    
    function handleClickOpen(value){
      const Projectdetails = document.getElementById('Project-Details'); 
      const Active = document.getElementById('Active-card'); 
      const InActive = document.getElementById('InActive-card'); 
      const Archived = document.getElementById('Archived-card'); 
      const Invoiced = document.getElementById('Invoiced-card'); 
      
      
      if(value === 'Active'){
        Projectdetails.style.display = "none";
        Active.style.display = "block";
        InActive.style.display = "none";
        Archived.style.display = "none";
        Invoiced.style.display = "none";
      }else if(value === 'InActive'){
        Projectdetails.style.display = "none";
        Active.style.display = "none";
        InActive.style.display = "block";
        Archived.style.display = "none";
        Invoiced.style.display = "none";
      }else if(value === 'Archived'){
        Projectdetails.style.display = "none";
        Active.style.display = "none";
        InActive.style.display = "none";
        Archived.style.display = "block";
        Invoiced.style.display = "none";
      }else if(value === 'Invoiced'){
        Projectdetails.style.display = "none";
        Active.style.display = "none";
        InActive.style.display = "none";
        Archived.style.display = "none";
        Invoiced.style.display = "block";
      }else{
        Projectdetails.style.display = "block";
        Active.style.display = "none";
        InActive.style.display = "none";
        Archived.style.display = "none";
        Invoiced.style.display = "none";
      }
    }

    function handleClickClose(value){
      const Projectdetails = document.getElementById('Project-Details'); 
      const Active = document.getElementById('Active-card'); 
      const InActive = document.getElementById('InActive-card'); 
      const Archived = document.getElementById('Archived-card'); 
      const Invoiced = document.getElementById('Invoiced-card'); 
      Projectdetails.style.display = "block";
      Active.style.display = "none";
      InActive.style.display = "none";
      Archived.style.display = "none";
      Invoiced.style.display = "none";
    }
  </script>

  <script>
          const suppliers = <?php echo json_encode($suppliers); ?>;
          const supplierCounts = <?php echo json_encode($supplierCounts); ?>;

          const ctx2 = document.getElementById('pieChart').getContext('2d');

          const pieChart = new Chart( ctx2, {
              type: 'pie',
              data: {
                  labels: suppliers,
                  datasets: [{
                      data: supplierCounts,
                      backgroundColor: [
                          'rgba(255, 99, 132, 0.2)',
                          'rgba(54, 162, 235, 0.2)',
                          'rgba(255, 206, 86, 0.2)',
                          'rgba(75, 192, 192, 0.2)',
                          'rgba(153, 102, 255, 0.2)',
                          'rgba(255, 159, 64, 0.2)'
                      ],
                      borderColor: [
                          'rgba(255, 99, 132, 1)',
                          'rgba(54, 162, 235, 1)',
                          'rgba(255, 206, 86, 1)',
                          'rgba(75, 192, 192, 1)',
                          'rgba(153, 102, 255, 1)',
                          'rgba(255, 159, 64, 1)'
                      ],
                      legendColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                      ],
                      borderWidth: 1
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: true
              }
          });
  </script>

  <script>
          const dayData = <?php echo json_encode($day_data); ?>;
          const weekData = <?php echo json_encode($week_data); ?>;
          const monthData = <?php echo json_encode($month_data); ?>;
          const countries = <?php echo json_encode($countries); ?>;

          const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

          const ctx = document.getElementById('projectChart').getContext('2d');
          let projectChart;

          const createChart = (labels, data,varity) => {
              if (projectChart) {
                  projectChart.destroy();
              }
              projectChart = new Chart(ctx, {
                  type: 'bar',
                  data: {
                      labels: labels,
                      datasets: [{
                          label: varity,
                          data: data,
                          backgroundColor: 'rgba(75, 192, 192, 0.2)',
                          borderColor: 'rgba(75, 192, 192, 1)',
                          borderWidth: 1
                      }]
                  },
                  options: {
                      scales: {
                          y: {
                              beginAtZero: true
                          }
                      }
                  }
              });
          };

          const showChart = (type) => {
              let labels = [];
              let data = [];
              var varity = "Day";

              if (type === 'day') {
                  labels = dayData.map(item => item.day);
                  data = dayData.map(item => item.total_projects);
                  varity = "Day";
                  console.log(varity);
              } else if (type === 'week') {
                  labels = weekData.map(item => `Year ${item.year} Week ${item.week}`);
                  data = weekData.map(item => item.total_projects);
                  varity = "Week";
              } else if (type === 'month') {
                  labels = monthData.map(item => monthNames[parseInt(item.month) - 1]);
                  data = monthData.map(item => item.total_projects);
                  varity = "Month";
              }

              createChart(labels, data,varity);
          };

          // Show daily data by default
          showChart('day');
  </script>


<?php
  include '../include/footer.php';

?>
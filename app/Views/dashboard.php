<?php include "common/header.php"; ?>
<style>
  /* Make the table header smaller and brown */
  table thead th {
    padding: 6px 12px !important;
    font-size: 13px !important;
    background-color: #a1263a !important; /* Brown */
    color: #ffffff !important;
    vertical-align: middle;
    text-align: center;
  }

  /* Optional: Make table body font consistent */
  table tbody td {
    font-size: 14px;
    padding: 8px 12px;
    vertical-align: middle;
  }

  /* Optional: Add border for better visibility */
  table th, table td {
    border: 1px solid #dee2e6;
  }
</style>

<!-- partial -->
<div class="right_container">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="d-flex justify-content-between flex-wrap">
          <div class="d-flex align-items-end flex-wrap">
            <div class="me-md-3 me-xl-5">
              <h2>Welcome back</h2>
              <!-- <p class="mb-md-0">Alrai Printing Press</p> -->
            </div>
          </div>
          <div class="d-flex align-items-center flex-wrap">
            <i class="mdi mdi-home text-muted hover-cursor"></i>
            <span class="text-muted hover-cursor mx-1">/ Dashboard /</span>
            <span class="text-primary hover-cursor">Analytics</span>
          </div>


          <!-- <div class="d-flex justify-content-between align-items-end flex-wrap">
                  <button type="button" class="btn btn-light bg-white btn-icon me-3 d-none d-md-block ">
                    <i class="mdi mdi-download text-muted"></i>
                  </button>
                  <button type="button" class="btn btn-light bg-white btn-icon me-3 mt-2 mt-xl-0">
                    <i class="mdi mdi-clock-outline text-muted"></i>
                  </button>
                  <button type="button" class="btn btn-light bg-white btn-icon me-3 mt-2 mt-xl-0">
                    <i class="mdi mdi-plus text-muted"></i>
                  </button>
                  <button class="btn btn-primary mt-2 mt-xl-0">Download report</button>
                </div> -->
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body dashboard-tabs p-0">
            <!-- <ul class="nav nav-tabs px-4 border-left-0 border-top-0 border-right-0" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="sales-tab" data-bs-toggle="tab" href="#sales" role="tab" aria-controls="sales" aria-selected="false">Sales</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="purchases-tab" data-bs-toggle="tab" href="#purchases" role="tab" aria-controls="purchases" aria-selected="false">Purchases</a>
                    </li>
                  </ul> -->
            <div class="tab-content py-0 px-0 border-left-0 border-bottom-0 border-right-0">
              <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="d-flex flex-wrap justify-content-xl-between">
                  <?php
                  use App\Models\Manageuser_Model;
                  $userModel = new Manageuser_Model();
                  $userCount = $userModel->getAllUserCount()->totuser ?? 0;
                  ?>

                  <div class="d-flex flex-wrap justify-content-xl-between">
                    <a href="<?= base_url('adduserlist') ?>" class="text-decoration-none text-dark">
                      <!-- <div class=" d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item bg-primary text-white"
                        style="cursor: pointer;">
                        <div class="icon-box me-3">
                          <i class="mdi mdi-account-group"></i>
                        </div>
                        <div class="d-flex flex-column justify-content-around p-2 rounded ">
                          <small class="mb-1 text-white">Total Customers</small>
                          <h5 class="mb-0 text-center"><?= $userCount ?></h5>
                        </div>
                      </div> -->
                    </a>
                  </div>
                  <!-- <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item bg-primary text-white" style="height: 150px;"> -->
                    <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item bg-revenue"style="height: 150px;" >
                    <div class="icon-box me-4">
                      <i class="mdi mdi-currency-usd"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-3 text-white"><b>Total Revenue Of The Day</b></small>
                      <h5 class="me-2 mb-0 text-center">KWD 577545</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item bg-success text-white">
                    <div class="icon-box me-4">
                      <i class="mdi mdi-eye"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-3 text-white"><b>Total Expenses Of The Day</b></small>
                      <h5 class="me-2 mb-0 text-center" id="dailyExpense">0</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item bg-info text-white">
                    <div class="icon-box me-4">
                      <i class="mdi mdi-download"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-3 text-white"><b>Total Income Of The Month</b></small>
                      <h5 class="me-2 mb-0 text-center">0000</h5>
                    </div>
                  </div>
                  <div  class="d-flex py-3 border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item bg-warning text-white">
                    <div class="icon-box me-4">
                      <i class="mdi mdi-flag"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-3 text-white"><b>Total Expenses Of The Month</b></small>
                      <h5 class="me-2 mb-0 text-center" id="monthlyExpense">0</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
                <div class="d-flex flex-wrap justify-content-xl-between">
                  <div
                    class="d-none d-xl-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-calendar-heart"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Start date</small>
                      <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle p-0 bg-transparent border-0 text-dark shadow-none font-weight-medium"
                          href="#" role="button" id="dropdownMenuLinkB" data-bs-toggle="dropdown" aria-haspopup="true"
                          aria-expanded="false">
                          <h5 class="mb-0 d-inline-block">26 Jul 2018</h5>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLinkB">
                          <a class="dropdown-item" href="#">12 Aug 2018</a>
                          <a class="dropdown-item" href="#">22 Sep 2018</a>
                          <a class="dropdown-item" href="#">21 Oct 2018</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-currency-usd"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Revenue</small>
                      <h5 class="me-2 mb-0">$577545</h5>
                    </div>
                  </div>
                  <div
                    class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-eye"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Total views</small>
                      <h5 class="me-2 mb-0">9833550</h5>
                    </div>
                  </div>
                  <div
                    class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-download"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Downloads</small>
                      <h5 class="me-2 mb-0">2233783</h5>
                    </div>
                  </div>
                  <div
                    class="d-flex py-3 border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-flag"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Flagged</small>
                      <h5 class="me-2 mb-0">3497843</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="purchases" role="tabpanel" aria-labelledby="purchases-tab">
                <div class="d-flex flex-wrap justify-content-xl-between">
                  <div
                    class="d-none d-xl-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-calendar-heart"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Start date</small>
                      <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle p-0 bg-transparent border-0 text-dark shadow-none font-weight-medium"
                          href="#" role="button" id="dropdownMenuLinkC" data-bs-toggle="dropdown" aria-haspopup="true"
                          aria-expanded="false">
                          <h5 class="mb-0 d-inline-block">26 Jul 2018</h5>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLinkC">
                          <a class="dropdown-item" href="#">12 Aug 2018</a>
                          <a class="dropdown-item" href="#">22 Sep 2018</a>
                          <a class="dropdown-item" href="#">21 Oct 2018</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-currency-usd"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Revenue</small>
                      <h5 class="me-2 mb-0">$577545</h5>
                    </div>
                  </div>
                  <div
                    class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-eye"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Total views</small>
                      <h5 class="me-2 mb-0">9833550</h5>
                    </div>
                  </div>
                  <div
                    class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-download"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Downloads</small>
                      <h5 class="me-2 mb-0">2233783</h5>
                    </div>
                  </div>
                  <div
                    class="d-flex py-3 border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-flag"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Flagged</small>
                      <h5 class="me-2 mb-0">3497843</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- <div class="row">
            <div class="col-md-6 col-xl-3 grid-margin stretch-card">
              <div class="card">
                <div id="cashSalesCarousel" class="carousel slide card-carousel" data-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <div class="card-body border-bottom">
                        <p class="card-title">Cash sales</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="cash-sales-chart"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">30%</h2>
                              <i class="mdi mdi-chevron-up text-success icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Sales last month</p>
                            <h2 class="mb-0">8543</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.321%</small>
                              <i class="mdi mdi-chevron-up"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-primary icon-box-lg">
                              <i class="mdi mdi-wallet"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of July</h5>
                        <p class="text-muted mb-0">To start a blog, think of a topic about and first brainstorm party is ways to write details</p>
                      </div>
                    </div>
                    <div class="carousel-item">
                      <div class="card-body border-bottom">
                        <p class="card-title">Cash sales</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="cash-sales-chart-b"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">41%</h2>
                              <i class="mdi mdi-chevron-up text-success icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Sales last month</p>
                            <h2 class="mb-0">1133</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.387%</small>
                              <i class="mdi mdi-chevron-up"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-primary icon-box-lg">
                              <i class="mdi mdi-wallet"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of August</h5>
                        <p class="text-muted mb-0">To start a blog, think of a topic about and first brainstorm party is ways to write details</p>
                      </div>
                    </div>
                  </div>
                  <a class="carousel-control-prev bg-white" href="#cashSalesCarousel" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  </a>
                  <a class="carousel-control-next bg-white" href="#cashSalesCarousel" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  </a>
                </div>    
              </div>
            </div>
            <div class="col-md-6 col-xl-3 grid-margin stretch-card">
              <div class="card">
                <div id="monthlyIncomeCarousel" class="carousel slide card-carousel" data-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <div class="card-body border-bottom">
                        <p class="card-title">Monthly income</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="monthly-income-chart"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">16%</h2>
                              <i class="mdi mdi-chevron-up text-warning icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Sales income</p>
                            <h2 class="mb-0">20087</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.754%</small>
                              <i class="mdi mdi-chevron-up"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-warning icon-box-lg">
                              <i class="mdi mdi-credit-card"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of January</h5>
                        <p class="text-muted mb-0">The first is a non technical method which requires the use of adware removal software. Download free </p>
                      </div>
                    </div>
                    <div class="carousel-item">
                      <div class="card-body border-bottom">
                        <p class="card-title">Monthly income</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="monthly-income-chart-b"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">23%</h2>
                              <i class="mdi mdi-chevron-up text-warning icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Sales income</p>
                            <h2 class="mb-0">7368</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.489%</small>
                              <i class="mdi mdi-chevron-up"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-warning icon-box-lg">
                              <i class="mdi mdi-credit-card"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of February</h5>
                        <p class="text-muted mb-0">The first is a non technical method which requires the use of adware removal software. Download free </p>
                      </div>
                    </div>
                  </div>
                  <a class="carousel-control-prev bg-white" href="#monthlyIncomeCarousel" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  </a>
                  <a class="carousel-control-next bg-white" href="#monthlyIncomeCarousel" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  </a>
                </div>    
              </div>
            </div>
            <div class="col-md-6 col-xl-3 grid-margin stretch-card">
              <div class="card">
                <div id="yearlySalesCarousel" class="carousel slide card-carousel" data-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <div class="card-body border-bottom">
                        <p class="card-title">Yearly sales</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="yearly-sales-chart"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">52%</h2>
                              <i class="mdi mdi-chevron-down text-danger icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Purchases</p>
                            <h2 class="mb-0">8304</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.846%</small>
                              <i class="mdi mdi-chevron-down"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-info icon-box-lg">
                              <i class="mdi mdi-cart"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of March</h5>
                        <p class="text-muted mb-0">While most people enjoy casino gambling, sports betting, lottery and bingo playing for the fun and </p>
                      </div>
                    </div>
                    <div class="carousel-item">
                      <div class="card-body border-bottom">
                        <p class="card-title">Yearly sales</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="yearly-sales-chart-b"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">29%</h2>
                              <i class="mdi mdi-chevron-down text-danger icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Purchases</p>
                            <h2 class="mb-0">8543</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.795%</small>
                              <i class="mdi mdi-chevron-down"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-info icon-box-lg">
                              <i class="mdi mdi-cart"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of April</h5>
                        <p class="text-muted mb-0">While most people enjoy casino gambling, sports betting, lottery and bingo playing for the fun and </p>
                      </div>
                    </div>
                  </div>
                  <a class="carousel-control-prev bg-white" href="#yearlySalesCarousel" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  </a>
                  <a class="carousel-control-next bg-white" href="#yearlySalesCarousel" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  </a>
                </div>    
              </div>
            </div>
            <div class="col-md-6 col-xl-3 grid-margin stretch-card">
              <div class="card">
                <div id="dailyDepositsCarousel" class="carousel slide card-carousel" data-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <div class="card-body border-bottom">
                        <p class="card-title">Daily deposits</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="daily-deposits-chart"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">19%</h2>
                              <i class="mdi mdi-chevron-up text-success icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Security deposits</p>
                            <h2 class="mb-0">2388</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.321%</small>
                              <i class="mdi mdi-chevron-up"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-success icon-box-lg">
                              <i class="mdi mdi-calendar-heart"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of May</h5>
                        <p class="text-muted mb-0">According to the research firm Frost & Sullivan, the estimated size of the North American used test</p>
                      </div>
                    </div>
                    <div class="carousel-item">
                      <div class="card-body border-bottom">
                        <p class="card-title">Daily deposits</p>
                        <div class="row">
                          <div class="col-6">
                            <canvas id="daily-deposits-chart-b"></canvas>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center ms-2">
                              <h2 class="font-weight-bold mb-0 me-1">33%</h2>
                              <i class="mdi mdi-chevron-up text-success icon-md"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                          <div>
                            <p>Security deposits</p>
                            <h2 class="mb-0">7589</h2>
                            <div class="d-flex align-items-center mt-2">
                              <small>0.321%</small>
                              <i class="mdi mdi-chevron-up"></i>
                            </div>
                          </div>
                          <div>
                            <div class="icon-box-success icon-box-lg">
                              <i class="mdi mdi-calendar-heart"></i>
                            </div>
                          </div>
                        </div>
                        <h5>Gross sales of June</h5>
                        <p class="text-muted mb-0">According to the research firm Frost & Sullivan, the estimated size of the North American used test</p>
                      </div>
                    </div>
                  </div>
                  <a class="carousel-control-prev bg-white" href="#dailyDepositsCarousel" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  </a>
                  <a class="carousel-control-next bg-white" href="#dailyDepositsCarousel" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  </a>
                </div>    
              </div>
            </div>
          </div> -->
    <!-- <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Cash deposits</p>
                  <p class="mb-4">To start a blog, think of a topic about and first brainstorm party is ways to write details</p>
                  <div id="cash-deposits-chart-legend" class="d-flex justify-content-center pt-3 legend-small"></div>
                  <canvas id="cash-deposits-chart"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-5 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Total sales</p>
                  <h1>$ 28835</h1>
                  <h4>Gross sales over the years</h4>
                  <p class="text-muted">Today, many people rely on computers to do homework, work, and create or store useful information. Therefore, it is important </p>
                  <div id="total-sales-chart-legend" class="legend-small"></div>                  
                </div>
                <canvas id="total-sales-chart"></canvas>
              </div>
            </div>
          </div> -->
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <p class="card-title">Recent Estimates</p>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th><strong>Sl No</strong></th>
                    <th><strong>Date</strong></th>
                    <th><strong>Customer Name</strong></th>
                    <th><strong>Customer Address</strong></th>
                    <th><strong>Subtotal</strong></th>
                    <th><strong>Gross Amount</strong></th>
                  </tr>
                </thead>
                <tbody id="recentEstimatesBody"></tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php include "common/footer.php"; ?>
<script>
  function fetchTodayExpense() {
    $.ajax({
      url: "<?= base_url('dashboard/getTodayExpenseTotal') ?>",
      type: "POST",
      dataType: "json",
      success: function (res) {
        $('#dailyExpense').text(res.total);
      },
      error: function () {
        $('#dailyExpense').text('0');
      }
    });
  }

  function fetchMonthlyExpense() {
    $.ajax({
      url: "<?= base_url('dashboard/getMonthlyExpenseTotal') ?>",
      type: "POST",
      dataType: "json",
      success: function (res) {
        $('#monthlyExpense').text(res.total);
      },
      error: function () {
        $('#monthlyExpense').text('0');
      }
    });
  }

  function loadRecentEstimates() {
    $.ajax({
      url: "<?= base_url('estimate/recentEstimates') ?>",
      method: "GET",
      dataType: "json",
      success: function (data) {
        let rows = '';
        if (data.length > 0) {
          data.forEach((est, index) => {
            rows += `
              <tr>
                <td>${index + 1}</td>
                <td>${formatDate(est.date)}</td>
                <td>${est.customer_name ?? '-'}</td>
                <td>${est.customer_address ?? '-'}</td>
                <td>${parseFloat(est.sub_total || 0).toFixed(2)} KWD</td>
                <td>${parseFloat(est.total_amount || 0).toFixed(2)} KWD</td>
              </tr>
            `;
          });
        } else {
          rows = '<tr><td colspan="6" class="text-center">No recent estimates found.</td></tr>';
        }
        $('#recentEstimatesBody').html(rows);
      },
      error: function () {
        $('#recentEstimatesBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading estimates.</td></tr>');
      }
    });
  }
  function formatDate(dateStr) {
    const date = new Date(dateStr);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
  }

  $(document).ready(function () {
    fetchTodayExpense();
    fetchMonthlyExpense();
    loadRecentEstimates();

    // Auto-refresh every 10 seconds
    setInterval(function () {
      fetchTodayExpense();
      fetchMonthlyExpense();
      loadRecentEstimates();
    }, 10000);
  });
</script>

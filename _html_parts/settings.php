<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
if (!in_array($GLOBALS['CONFIG']['PERMISSIONS']['SETTINGS_PAGE'], $this->permissions)) {
    die($this->lang_php['not_permissions']);
}
require 'templates/list_users.php';

$this->title = $_SERVER['HTTP_HOST'] . ' - ' . $this->lang_php['title_settings'];

$projects_list = $this->getProjects();
?>
<script src="<?= base_url('assets/js/ckeditor/ckeditor.js') ?>"></script>
<div id="settings">
    <nav class="navbar navbar-settings navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#"><?= $this->lang_php['general_settings'] ?></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?= base_url('settings') ?>"><?= $this->lang_php['overview'] ?></a></li>
                    <li><a href="<?= base_url('profile/' . $this->user_name) ?>"><?= $this->lang_php['profile'] ?></a></li>
                    <li><a href="<?= base_url('home') ?>"><?= $this->lang_php['home'] ?></a></li>
                    <li><a href="<?= base_url('documentation.html') ?>" target="_blank"><?= $this->lang_php['help'] ?></a></li>
                </ul>
                <form class="navbar-form navbar-right form-s">
                    <input type="text" class="form-control" name="settings_search" placeholder="<?= $this->lang_php['search'] ?>">
                    <div class="list-group" id="settings-query-result">

                    </div>
                    <img src="<?= base_url('assets/imgs/settings-search-spinner.gif') ?>" alt="loading" class="s-loading">
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <li class="<?= url_segment(1) === false ? 'active' : '' ?>"><a href="<?= base_url('settings') ?>"><?= $this->lang_php['overview'] ?> <span class="sr-only">(current)</span></a></li>
                    <li class="<?= url_segment(1) === 'general' ? 'active' : '' ?>"><a href="<?= base_url('settings/general') ?>"><?= $this->lang_php['general'] ?> <span class="sr-only">(current)</span></a></li>
                    <li class="<?= url_segment(1) == 'projects' ? 'active' : '' ?>"><a href="<?= base_url('settings/projects') ?>"><?= $this->lang_php['projects'] ?></a></li>
                    <li class="<?= url_segment(1) == 'profiles' ? 'active' : '' ?>"><a href="<?= base_url('settings/profiles') ?>"><?= $this->lang_php['profiles'] ?></a></li>
                    <li class="<?= url_segment(1) == 'syncing' ? 'active' : '' ?>"><a href="<?= base_url('settings/syncing') ?>"><?= $this->lang_php['emails_syncing'] ?></a></li>
                    <li class="<?= url_segment(1) == 'logs' ? 'active' : '' ?>"><a href="<?= base_url('settings/logs') ?>"><?= $this->lang_php['logs'] ?></a></li>
                </ul>
                <ul class="nav nav-sidebar">
                    <li class="<?= url_segment(1) == 'wiki_spaces' ? 'active' : '' ?>"><a href="<?= base_url('settings/wiki_spaces') ?>"><?= $this->lang_php['wiki_spaces'] ?></a></li>
                    <li class="<?= url_segment(1) == 'wiki_templates' ? 'active' : '' ?>"><a href="<?= base_url('settings/wiki_templates') ?>"><?= $this->lang_php['wiki_templates'] ?></a></li>
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?php if (url_segment(1) === false) { ?>
                    <?php
                    $find = null;
                    if (isset($_GET['gofilter'])) {
                        $find = $_GET;
                    }

                    //TICKETS
                    $res_tickets = $this->getTicketStatistics($find);

                    if (!empty($res_tickets)) {
                        $months_categories = "'" . implode("','", array_keys($res_tickets['months'])) . "'";

                        if ($res_tickets['num_created'] != null) {
                            $months_created = "";
                            foreach ($res_tickets['num_created'] as $p => $m) {
                                $color[] = $m['color'];
                                $months_created .= "{";
                                $months_created .= "name: '$p',";
                                $months_created .= "data: [" . implode(', ', $m['nums']) . "]";
                                $months_created .= "},";
                            }
                            $colors = "'" . implode("', '", $color) . "'";
                            $months_created = rtrim($months_created, ",");
                        }

                        if ($res_tickets['num_closed'] != null) {
                            $months_closed = "";
                            foreach ($res_tickets['num_closed'] as $p => $m) {
                                $color_c[] = $m['color'];
                                $months_closed .= "{";
                                $months_closed .= "name: '$p',";
                                $months_closed .= "data: [" . implode(', ', $m['nums']) . "]";
                                $months_closed .= "},";
                            }
                            $colors_c = "'" . implode("', '", $color_c) . "'";
                            $months_closed = rtrim($months_closed, ",");
                        }

                        $percents_tickets = '';
                        foreach ($res_tickets['num_for_priority'] as $pr => $num_p) {
                            $division = $num_p / $res_tickets['num_all'];
                            $percent = $division * 100;

                            $percents_tickets .= "{";
                            $percents_tickets .= "name: '$pr',";
                            $percents_tickets .= "y: " . number_format($percent);
                            $percents_tickets .= "},";
                        }
                        $percents_tickets = rtrim($percents_tickets, ",");
                    }

                    //WIKI
                    $res_wiki = $this->getWikiStatistics($find);
                    if (!empty($res_wiki)) {
                        $months_categories_w = "'" . implode("','", array_keys($res_wiki['months'])) . "'";
                        if ($res_wiki['num_created'] != null) {
                            $months_created_w = "";
                            foreach ($res_wiki['num_created'] as $p_w => $m_w) {
                                $months_created_w .= "{";
                                $months_created_w .= "name: '$p_w',";
                                $months_created_w .= "data: [" . implode(', ', $m_w) . "]";
                                $months_created_w .= "},";
                            }
                            $months_created_w = rtrim($months_created_w, ",");
                        }
                    }

                    $theLog = $this->getSettingsActivityLog();
                    require 'templates/activitystream.php';
                    ?>
                    <div id="settings-overview">
                        <h1><?= $this->lang_php['overview'] ?>:</h1>
                        <hr>
                        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-datepicker.min.css') ?>">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#settings-stat-tab" aria-controls="settings-stat-tab" role="tab" data-toggle="tab"><?= $this->lang_php['statistics'] ?></a></li>
                            <li role="presentation"><a href="#settings-activ-tab" aria-controls="settings-activ-tab" role="tab" data-toggle="tab"><?= $this->lang_php['activity_log'] ?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="settings-stat-tab">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form method="GET" action="">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <select name="project" class="selectpicker form-control show-menu-arrow">
                                                        <option value=""><?= $this->lang_php['all_projects'] ?></option>
                                                        <?php foreach ($projects_list as $proj) { ?>
                                                            <option <?= isset($_GET['project']) && $_GET['project'] == $proj['id'] ? 'selected' : '' ?> value="<?= $proj['id'] ?>"><?= $proj['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <span id="from" class="input-group-addon"><?= $this->lang_php['from'] ?>:</span>
                                                        <input class="form-control date-pick" type="text" aria-describedby="from" placeholder="<?= $this->lang_php['date'] ?>" value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>" name="from_date">
                                                        <span id="to" class="input-group-addon"><?= $this->lang_php['to'] ?>:</span>
                                                        <input class="form-control date-pick" type="text" aria-describedby="to" placeholder="<?= $this->lang_php['date'] ?>" value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>" name="to_date">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <input type="submit" class="btn btn-primary" value="<?= $this->lang_php['filter_it'] ?>" name="gofilter">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <h1><?= $this->lang_php['statistics'] ?><sup><?= $this->lang_php['tickets'] ?></sup></h1>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <script>
                                            $(function () {
                                                /**
                                                 * Sand-Signika theme for Highcharts JS
                                                 * @author Torstein Honsi
                                                 */

                                                // Load the fonts
                                                Highcharts.createElement('link', {
                                                    href: '//fonts.googleapis.com/css?family=Signika:400,700',
                                                    rel: 'stylesheet',
                                                    type: 'text/css'
                                                }, null, document.getElementsByTagName('head')[0]);

                                                // Add the background image to the container
                                                Highcharts.wrap(Highcharts.Chart.prototype, 'getContainer', function (proceed) {
                                                    proceed.call(this);
                                                    this.container.style.background = 'url(http://www.highcharts.com/samples/graphics/sand.png)';
                                                });


                                                Highcharts.theme = {
                                                    colors: ["#f45b5b", "#8085e9", "#8d4654", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
                                                        "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
                                                    chart: {
                                                        backgroundColor: null,
                                                        style: {
                                                            fontFamily: "Signika, serif"
                                                        }
                                                    },
                                                    title: {
                                                        style: {
                                                            color: 'black',
                                                            fontSize: '16px',
                                                            fontWeight: 'bold'
                                                        }
                                                    },
                                                    subtitle: {
                                                        style: {
                                                            color: 'black'
                                                        }
                                                    },
                                                    tooltip: {
                                                        borderWidth: 0
                                                    },
                                                    legend: {
                                                        itemStyle: {
                                                            fontWeight: 'bold',
                                                            fontSize: '13px'
                                                        }
                                                    },
                                                    xAxis: {
                                                        labels: {
                                                            style: {
                                                                color: '#6e6e70'
                                                            }
                                                        }
                                                    },
                                                    yAxis: {
                                                        labels: {
                                                            style: {
                                                                color: '#6e6e70'
                                                            }
                                                        }
                                                    },
                                                    plotOptions: {
                                                        series: {
                                                            shadow: true
                                                        },
                                                        candlestick: {
                                                            lineColor: '#404048'
                                                        },
                                                        map: {
                                                            shadow: false
                                                        }
                                                    },
                                                    // Highstock specific
                                                    navigator: {
                                                        xAxis: {
                                                            gridLineColor: '#D0D0D8'
                                                        }
                                                    },
                                                    rangeSelector: {
                                                        buttonTheme: {
                                                            fill: 'white',
                                                            stroke: '#C0C0C8',
                                                            'stroke-width': 1,
                                                            states: {
                                                                select: {
                                                                    fill: '#D0D0D8'
                                                                }
                                                            }
                                                        }
                                                    },
                                                    scrollbar: {
                                                        trackBorderColor: '#C0C0C8'
                                                    },
                                                    // General
                                                    background2: '#E0E0E8'

                                                };

                                                // Apply the theme
                                                Highcharts.setOptions(Highcharts.theme);
                                            });
                                        </script>
                                        <?php
                                        if (!empty($res_tickets) && $res_tickets['num_created'] != null) {
                                            ?>
                                            <script>
                                                $(function () {
                                                    $('#monthly-created-tickets').highcharts({
                                                        title: {
                                                            text: lang.highcharts_monthly_created,
                                                            x: -20
                                                        },
                                                        colors: [<?= $colors ?>],
                                                        xAxis: {
                                                            categories: [<?= $months_categories ?>]
                                                        },
                                                        yAxis: {
                                                            title: {
                                                                text: 'Number'
                                                            },
                                                            plotLines: [{
                                                                    value: 0,
                                                                    width: 1,
                                                                    color: '#808080'
                                                                }]
                                                        },
                                                        tooltip: {
                                                            valueSuffix: ''
                                                        },
                                                        legend: {
                                                            layout: 'vertical',
                                                            align: 'right',
                                                            verticalAlign: 'middle',
                                                            borderWidth: 0
                                                        },
                                                        series: [<?= $months_created ?>]
                                                    });
                                                });
                                            </script>
                                            <div id="monthly-created-tickets"></div>
                                            <hr>
                                        <?php } else { ?>
                                            <div class="alert alert-info"><?= $this->lang_php['no_stat_for_tickets'] ?></div>
                                            <?php
                                        }
                                        if (!empty($res_tickets) && $res_tickets['num_closed'] != null) {
                                            ?>
                                            <script>$(function () {
                                                    $('#monthly-closed-tickets').highcharts({
                                                        title: {
                                                            text: lang.highcharts_monthly_closed,
                                                            x: -20
                                                        },
                                                        colors: [<?= $colors_c ?>],
                                                        xAxis: {
                                                            categories: [<?= $months_categories ?>]
                                                        },
                                                        yAxis: {
                                                            title: {
                                                                text: 'Number'
                                                            },
                                                            plotLines: [{
                                                                    value: 0,
                                                                    width: 1,
                                                                    color: '#808080'
                                                                }]
                                                        },
                                                        tooltip: {
                                                            valueSuffix: ''
                                                        },
                                                        legend: {
                                                            layout: 'vertical',
                                                            align: 'right',
                                                            verticalAlign: 'middle',
                                                            borderWidth: 0
                                                        },
                                                        series: [<?= $months_closed ?>]
                                                    });
                                                });
                                            </script>
                                            <div id="monthly-closed-tickets"></div>
                                            <hr>
                                        <?php } else { ?>
                                            <div class="alert alert-info"><?= $this->lang_php['no_stat_for_tickets_closed'] ?></div>
                                            <?php
                                        }

                                        if (isset($percents_tickets) && $percents_tickets != null) {
                                            ?>
                                            <script>
                                                $(function () {
                                                    $('#tickets-with-priorities').highcharts({
                                                        chart: {
                                                            plotBackgroundColor: null,
                                                            plotBorderWidth: null,
                                                            plotShadow: false,
                                                            type: 'pie'
                                                        },
                                                        title: {
                                                            text: lang.highcharts_by_priority
                                                        },
                                                        colors: [<?= $colors ?>],
                                                        tooltip: {
                                                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                                                        },
                                                        plotOptions: {
                                                            pie: {
                                                                allowPointSelect: true,
                                                                cursor: 'pointer',
                                                                dataLabels: {
                                                                    enabled: false
                                                                },
                                                                showInLegend: true
                                                            }
                                                        },
                                                        series: [{
                                                                name: lang.highcharts_priority,
                                                                colorByPoint: true,
                                                                data: [<?= $percents_tickets ?>]
                                                            }]
                                                    });
                                                });
                                            </script>
                                            <div id="tickets-with-priorities"></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <h1><?= $this->lang_php['statistics'] ?><sup><?= $this->lang_php['wiki'] ?></sup></h1>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?php
                                        if (!empty($res_wiki)) {
                                            ?>
                                            <script>
                                                $(function () {
                                                    $('#monthly-created-pages').highcharts({
                                                        title: {
                                                            text: lang.highcharts_monthly_created_pages,
                                                            x: -20
                                                        },
                                                        xAxis: {
                                                            categories: [<?= $months_categories_w ?>]
                                                        },
                                                        yAxis: {
                                                            title: {
                                                                text: 'Number'
                                                            },
                                                            plotLines: [{
                                                                    value: 0,
                                                                    width: 1,
                                                                    color: '#808080'
                                                                }]
                                                        },
                                                        tooltip: {
                                                            valueSuffix: ' pages'
                                                        },
                                                        legend: {
                                                            layout: 'vertical',
                                                            align: 'right',
                                                            verticalAlign: 'middle',
                                                            borderWidth: 0
                                                        },
                                                        series: [<?= $months_created_w ?>]
                                                    });
                                                });
                                            </script>
                                            <div id="monthly-created-pages"></div>
                                        <?php } else { ?>
                                            <div class="alert alert-info"><?= $this->lang_php['no_stat_for_pages'] ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane fade" id="settings-activ-tab">
                                <h1><?= $this->lang_php['activity_log'] ?><sup><?= $this->lang_php['all'] ?></sup></h1>
                                <div class="panel panel-info panel-activity">
                                    <div class="panel-heading"><?= $this->lang_php['activity'] ?></div>
                                    <div class="panel-body">
                                        <?php
                                        if (!empty($theLog)) {
                                            activityForeach($theLog, 2);
                                            ?>
                                            <div id="w-ajax"></div>
                                            <a href="javascript:void(0)" onClick="showMore(<?= $this->project_id ?>)" class="bordered text-center show-more"><?= $this->lang_php['show_more'] ?> <i class="fa fa-chevron-circle-down"></i></a>
                                            <?php
                                        } else {
                                            ?>
                                            <?= $this->lang_php['no_activity'] ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="<?= base_url('assets/js/bootstrap-datepicker.min.js') ?>"></script>
                    <script src="<?= base_url('assets/js/highcharts/highcharts.js') ?>"></script>
                    <script src="<?= base_url('assets/js/highcharts/modules/exporting.js') ?>"></script>
                    <script>
                                                if (window.location.hash == '#settings-activ-tab') {
                                                    alert();
                                                }

                                                function goAjax(projectid, from, to) {
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "activity_settings",
                                                        data: {from: from, to: to}
                                                    }).done(function (data) {
                                                        $("#w-ajax").append(data);
                                                    });
                                                }
                                                to = 10;
                                                from = 0;
                                                function showMore(projectid) {
                                                    to += 10;
                                                    from += 10;
                                                    goAjax(projectid, from, to);
                                                }

                                                $('.date-pick').datepicker({
                                                    calendarWeeks: true,
                                                    autoclose: true,
                                                    todayHighlight: true,
                                                    format: 'dd.mm.yyyy'
                                                });
                    </script>
                    <?php
                }
                if (url_segment(1) !== false && url_segment(1) == 'general') {
                    $abbreviations = getFolderLanguages();
                    if (isset($_POST['setlanguage'])) {
                        $result = $this->setLanguage($_POST['language']);
                        if ($result === true) {
                            $this->set_alert($this->lang_php['language_changed'] . '!', 'success');
                        } else {
                            $this->set_alert($this->lang_php['language_changed_err'] . '!', 'danger');
                        }
                        redirect('general');
                    }
                    if (isset($_POST['setloginimage'])) {
                        $img_name = $this->uploadImage($GLOBALS['CONFIG']['IMAGELOGINUPLOADDIR']);
                        $result = $this->setLoginImage($img_name);
                        if ($result === true) {
                            $this->set_alert($this->lang_php['login_image_upload'] . '!', 'success');
                        } else {
                            $this->set_alert($this->lang_php['login_image_upload_err'] . '!', 'danger');
                        }
                        redirect('general');
                    }
                    $login_image = $this->getLoginImage();
                    if (isset($_GET['removeLoginImage'])) {
                        $this->removeLoginImage($GLOBALS['CONFIG']['IMAGELOGINUPLOADDIR'] . $login_image);
                        redirect('general');
                    }
                    if (isset($_POST['setDefCurrency'])) {
                        $result = $this->setDefCurrency($_POST['def_currency']);
                        if ($result === true) {
                            $this->set_alert($this->lang_php['currency_changed'] . '!', 'success');
                        } else {
                            $this->set_alert($this->lang_php['currency_change_err'] . '!', 'danger');
                        }
                        redirect('general');
                    }
                    $currencies = $this->getCurrencies();
                    echo $this->get_alert();
                    ?>
                    <h1><?= $this->lang_php['general'] ?>:</h1>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading"><?= $this->lang_php['site_default_language'] ?></div>
                                <div class="panel-body">
                                    <form method="post" action="">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
                                                <select name="language" class="selectpicker form-control show-tick show-menu-arrow">
                                                    <?php foreach ($abbreviations as $abbreviature) { ?>
                                                        <option <?= $abbreviature == $this->default_lang_abbr ? 'selected' : '' ?> value="<?= $abbreviature ?>"><?= $abbreviature ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                                                <input type="submit" name="setlanguage" class="btn btn-info" value="<?= $this->lang_php['save'] ?>">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading"><?= $this->lang_php['login_image'] ?></div>
                                <div class="panel-body">
                                    <form method="post" enctype="multipart/form-data" action="">
                                        <div class="col-xs-8">
                                            <div class="form-group">
                                                <img class="profile-img" src="<?= $login_image != null ? base_url($GLOBALS['CONFIG']['IMAGELOGINUPLOADDIR'] . $login_image) : '' ?>" alt="no image" style="width:90px;">
                                                <label class="col-sm-2 control-label"><?= $this->lang_php['image'] ?></label>
                                                <div class="col-sm-10">
                                                    <input type="file" name="image" id="fileToUpload">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <input type="submit" name="setloginimage" class="btn btn-info" value="<?= $this->lang_php['save'] ?>">
                                            <?php if ($login_image != null) { ?>
                                                <a href="?removeLoginImage=true" class="btn btn-danger"><?= $this->lang_php['remove'] ?></a>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading"><?= $this->lang_php['price_per_hour'] . '/' . $this->lang_php['default_currency'] ?></div>
                                <div class="panel-body">
                                    <form method="post" action="">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
                                                <select name="def_currency" class="selectpicker form-control show-tick show-menu-arrow">
                                                    <?php foreach ($currencies as $currencie) { ?>
                                                        <option <?= $currencie['def'] == 1 ? 'selected' : '' ?> value="<?= $currencie['currency'] ?>"><?= $currencie['country'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                                                <input type="submit" name="setDefCurrency" class="btn btn-info" value="<?= $this->lang_php['save'] ?>">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                if (url_segment(1) !== false && url_segment(1) == 'profiles') {
                    echo $this->get_alert();
                    ?>
                    <h1><?= $this->lang_php['profiles'] ?>:</h1>
                    <hr>
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#addUser">
                        <?= $this->lang_php['add_user'] ?>
                    </button>
                    <div class="profiles-boxes">
                        <hr>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="panel panel-default">
                                    <div class="panel-body p-t-0">
                                        <form method="GET" action="">
                                            <div class="input-group">
                                                <input type="text" id="example-input1-group2" value="<?= isset($_GET['find-user']) ? urldecode($_GET['find-user']) : '' ?>" name="find-user" class="form-control" placeholder="Search">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-effect-ripple btn-primary"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            $abbreviations = getFolderLanguages();
                            if (isset($_POST['update'])) {
                                $img_name = $this->uploadImage($GLOBALS['CONFIG']['IMAGESUSERSUPLOADDIR']);
                                if ($img_name !== null) {
                                    $_POST['image'] = $img_name;
                                }
                                $result = $this->setUser($_POST);
                                if ($result == false) {
                                    $this->set_alert($this->lang_php['db_err'], 'danger');
                                } else {
                                    $this->set_alert($this->lang_php['user_updated'], 'success');
                                }
                                redirect('profiles' . getQueryStr($_GET, 'page'));
                            }

                            if (isset($_GET['delete_user'])) {
                                $this->deleteUser($_GET['delete_user']);
                                redirect('profiles' . getQueryStr($_GET, array('page', 'delete_user')));
                            }

                            $search = isset($_GET['find-user']) ? $_GET['find-user'] : null;
                            $num_results = $this->getCountProfiles($search);

                            $num_pages = ceil($num_results / RESULT_LIMIT_SETTINGS_PROFILES);
                            $pg = isset($_GET['pg']) ? intval($_GET['pg']) : 0;
                            $pg = min($pg, $num_pages);
                            $pg = max($pg, 0);
                            $from = RESULT_LIMIT_SETTINGS_PROFILES * ($pg);

                            $profiles = $this->getUsers($from, RESULT_LIMIT_SETTINGS_PROFILES, $search);
                            $professions = $this->getProfessions();
                            $arr_lang = array(
                                'registered' => $this->lang_php['registered'],
                                'last_login' => $this->lang_php['last_login'],
                                'last_active' => $this->lang_php['last_active'],
                                'preview' => $this->lang_php['preview']
                            );
                            if (!empty($profiles)) {
                                loop_users($profiles, true, $arr_lang);
                                if (isset($_GET['find-user']) && $_GET['find-user'] != '') {
                                    $query_string = '&find-user=' . $_GET['find-user'];
                                } else {
                                    $query_string = '';
                                }
                            } else {
                                ?>
                                <h3><?= $this->lang_php['no_users_found'] ?> <?= isset($_GET['find-user']) && $_GET['find-user'] != '' ? $this->lang_php['with_name'] . ' <i>' . $_GET['find-user'] . '</i>!' : '!' ?></h3>
                            <?php } ?>
                        </div>
                        <?= paging('settings/profiles', $query_string, $num_results, $pg, RESULT_LIMIT_SETTINGS_PROFILES) ?>
                    </div>
                    <!-- Add User Modal -->
                    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['add_profile'] ?></h4>
                                </div>
                                <div class="modal-body">
                                    <form class="form-horizontal" id="user-add" role="form" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="update" value="0">
                                        <div class="alert alert-danger" id="reg-errors" style="display:none;"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['username'] ?> *</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="username" type="text" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['pass'] ?> *</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" id="pwd" name="password" type="text" value="">
                                                <span><?= $this->lang_php['pass_strenght'] ?>:</span>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0;">
                                                    </div>
                                                </div>
                                                <button type="button" id="GeneratePwd" class="btn btn-default"><?= $this->lang_php['gen_pass'] ?></button> 
                                                <p id="demo"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['full_name'] ?> *</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="fullname" type="text" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <img src="" alt="none" style="display:none;" id="cover">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['image'] ?></label>
                                            <div class="col-sm-10">
                                                <input type="file"  name="image" id="fileToUpload">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Facebook <i class="fa fa-facebook-official"></i></label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="facebook" type="text" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Twitter <i class="fa fa-twitter-square"></i></label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="twitter" type="text" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Linked In <i class="fa fa-linkedin-square"></i></label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="linkedin" type="text" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Skype <i class="fa fa-skype"></i></label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="skype" type="text" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email * <i class="fa fa-envelope"></i></label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="email" type="text" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['language'] ?></label>
                                            <div class="col-sm-10">
                                                <select name="lang" class="form-control" disabled>
                                                    <?php foreach ($abbreviations as $abbreviature) { ?>
                                                        <option <?= $abbreviature == $this->default_lang_abbr ? 'selected' : '' ?> value="<?= $abbreviature ?>"><?= $abbreviature ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" value="" id="default_lang" checked=""><?= $this->lang_php['use_site_default'] ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['profession'] ?></label>
                                            <div class="col-sm-10">
                                                <input type="hidden" value="0" name="new-prof">
                                                <input class="form-control" id="add-new-prof-field" name="new-prof-name" style="display:none;" type="text" value="">
                                                <select class="form-control" name="prof" id="prof-list">
                                                    <option value="0" selected="selected"></option>
                                                    <?php foreach ($professions as $prof) { ?>
                                                        <option value="<?= $prof['id'] ?>"><?= $prof['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                                <a href="javascript:void(0)" id="add-new-prof" class="btn btn-default"><?= $this->lang_php['add_new'] ?></a>
                                                <a href="javascript:void(0)" id="close-prof-list" class="btn btn-default" style="display:none;"><?= $this->lang_php['show_professions_list'] ?></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['can_see_projects'] ?></label>
                                            <div class="col-sm-10">
                                                <label class="checkbox-inline"><input type="checkbox" id="select-all-projs" /><?= $this->lang_php['select_all'] ?></label>
                                                <?php foreach ($projects_list as $proj) { ?>
                                                    <label class="checkbox-inline"><input type="checkbox" class="proj-checkbox" name="can_see_proj[]" value="<?= $proj['id'] ?>"><?= $proj['name'] ?></label>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?= $this->lang_php['user_type'] ?></label>
                                            <div class="col-sm-10">
                                                <input type="hidden" value="0" name="costom-privileges">
                                                <select class="form-control" name="default-priv" id="privileges">
                                                    <?php foreach ($GLOBALS['CONFIG']['DEFAULT_USER_TYPES'] as $ukey => $utype) { ?>
                                                        <option <?= $ukey == 'User' ? 'selected' : '' ?> value="<?= $utype ?>"><?= $ukey ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div id="privileges-types" style="display:none;">
                                                    <p><b><?= $this->lang_php['general'] ?></b></p>
                                                    <?php
                                                    foreach ($GLOBALS['CONFIG']['PERMISSIONS'] as $pkey => $pval) {
                                                        if (!is_array($pval)) {
                                                            ?>
                                                            <label class="checkbox-inline"><input type="checkbox" class="usrtype-checkbox" name="custom-priv[]" value="<?= $pval ?>" /><?= $pkey ?></label>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <p><b><?= $this->lang_php['tickets'] ?></b></p>
                                                    <?php
                                                    foreach ($GLOBALS['CONFIG']['PERMISSIONS']['TICKETS'] as $pkey => $pval) {
                                                        if (!is_array($pval)) {
                                                            ?>
                                                            <label class="checkbox-inline"><input type="checkbox" class="usrtype-checkbox" name="custom-priv[]" value="<?= $pval ?>" /><?= $pkey ?></label>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <p><b><?= $this->lang_php['wiki'] ?></b></p>
                                                    <?php
                                                    foreach ($GLOBALS['CONFIG']['PERMISSIONS']['WIKI'] as $pkey => $pval) {
                                                        if (!is_array($pval)) {
                                                            ?>
                                                            <label class="checkbox-inline"><input type="checkbox" class="usrtype-checkbox" name="custom-priv[]" value="<?= $pval ?>" /><?= $pkey ?></label>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <a href="javascript:void(0)" id="custom-privileges" class="btn btn-default"><?= $this->lang_php['custom_privileges'] ?></a>
                                                <a href="javascript:void(0)" id="custom-privileges-cancel" style="display:none;" class="btn btn-default"><?= $this->lang_php['default_privileges'] ?></a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                                    <button type="button" onclick="validateForm()" class="btn btn-primary"><?= $this->lang_php['save'] ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript" src="<?= base_url('assets/js/zxcvbn.js') ?>"></script>
                    <script type="text/javascript" src="<?= base_url('assets/js/zxcvbn_bootstrap3.js') ?>"></script>
                    <script type="text/javascript" src="<?= base_url('assets/js/pGenerator.jquery.js') ?>"></script>
                    <script>
                                        $(document).ready(function () {
                                            //PassStrength 
                                            checkPass();
                                            $("#pwd").on('keyup', function () {
                                                checkPass();
                                            });
                                            //PassGenerator
                                            $('#GeneratePwd').pGenerator({
                                                'bind': 'click',
                                                'passwordLength': 9,
                                                'uppercase': true,
                                                'lowercase': true,
                                                'numbers': true,
                                                'specialChars': false,
                                                'onPasswordGenerated': function (generatedPassword) {
                                                    $("#pwd").val(generatedPassword);
                                                    checkPass();
                                                }
                                            });
                                            $("#add-new-prof").click(function () {
                                                $("#prof-list, #add-new-prof").hide();
                                                $("#add-new-prof-field, #close-prof-list").show();
                                                $("[name='new-prof']").val(1);
                                            });
                                            $("#close-prof-list").click(function () {
                                                $("#prof-list, #add-new-prof").show();
                                                $("#add-new-prof-field, #close-prof-list").hide();
                                                $("[name='new-prof']").val(0);
                                            });
                                            $('#select-all-projs').click(function (event) {
                                                if (this.checked) {
                                                    var ch = true;
                                                } else {
                                                    var ch = false;
                                                }
                                                $('.proj-checkbox').each(function () {
                                                    this.checked = ch;
                                                });
                                            });
                                            $("#custom-privileges").click(function () {
                                                $("#custom-privileges-cancel, #privileges-types").show();
                                                $("#privileges, #custom-privileges").hide();
                                                $("[name='costom-privileges']").val(1);
                                            });
                                            $("#custom-privileges-cancel").click(function () {
                                                $("#custom-privileges-cancel, #privileges-types").hide();
                                                $("#privileges, #custom-privileges").show();
                                                $("[name='costom-privileges']").val(0);
                                            });
                                            $('#default_lang').click(function () {
                                                if ($(this).prop("checked") == true) {
                                                    $("[name='lang']").prop("disabled", true);
                                                } else {
                                                    $("[name='lang']").prop("disabled", false);
                                                }
                                            });
                                        });
                                        function takenCheck(name) {
                                            var output;
                                            $.ajax({
                                                type: "POST",
                                                async: false,
                                                url: "<?= base_url('getuserinfo') ?>",
                                                data: {uname: name}
                                            }).done(function (data) {
                                                if (data == 1) {
                                                    output = true;
                                                }
                                            });
                                            return output;
                                        }

                                        function validateForm() {
                                            var usr = $("[name='username']").val();
                                            var pwd = $("[name='password']").val();
                                            var fname = $("[name='fullname']").val();
                                            var email = $('[name="email"]').val();
                                            var reg = <?= $GLOBALS['CONFIG']['EMAILREGEX'] ?>;
                                            var errors = new Array();
                                            if (jQuery.trim(usr).length < 4 || jQuery.trim(usr).length > 20 || jQuery.trim(usr).search(<?= $GLOBALS['CONFIG']['USERNAMEREGEX'] ?>) === -1) {
                                                errors[0] = '<strong>' + lang.username_wrong + '</strong> ' + lang.username_must_be;
                                            } else if (takenCheck(jQuery.trim(usr)) != true && $("[name='update']").val() == 0) {
                                                errors[1] = '<strong>' + lang.username_taken + '</strong> ' + lang.username_schoose_another;
                                            }
                                            if ((checkPass() == false && jQuery.trim(pwd).length > 0 && $("[name='update']").val() > 0) || (checkPass() == false && $("[name='update']").val() == 0)) {

                                                errors[2] = '<strong>' + lang.wrong_pass + '</strong> ' + lang.pass_valid_progress;
                                            }
                                            if (jQuery.trim(usr).length > 30 || jQuery.trim(fname).search(<?= $GLOBALS['CONFIG']['FULLNAMEREGEX'] ?>) === -1) {
                                                errors[3] = '<strong>' + lang.fullname_wrong + '</strong> ' + lang.fullname_validation;
                                            }
                                            if (!reg.test(jQuery.trim(email))) {
                                                errors[4] = '<strong>' + lang.invalid_email + '</strong> ' + lang.use_valid_email;
                                            }
                                            if (errors.length > 0) {
                                                $("#reg-errors").show();
                                                for (i = 0; i < errors.length; i++) {
                                                    if (i == 0)
                                                        $("#reg-errors").empty();
                                                    if (typeof errors[i] !== 'undefined') {
                                                        $("#reg-errors").append(errors[i] + "<br>");
                                                    }
                                                }
                                                $('.modal-open .modal').scrollTop(0);
                                            } else {
                                                document.getElementById("user-add").submit();
                                            }

                                        }
                                        $('#addUser').on('hidden.bs.modal', function () {
                                            document.getElementById("user-add").reset();
                                            checkPass()
                                            $("#prof-list, #add-new-prof").show();
                                            $("#add-new-prof-field, #close-prof-list").hide();
                                            $("[name='new-prof']").val(0);
                                            $("#custom-privileges-cancel, #privileges-types").hide();
                                            $("#privileges, #custom-privileges").show();
                                            $("[name='costom-privileges']").val(0);
                                            $("[name='update']").val(0);
                                            $("#reg-errors").hide();
                                            $("#cover").hide();
                                            $("[name='username']").prop('disabled', false);
                                            $("#GeneratePwd").text(lang.generate_pass);
                                            $("[name='lang']").prop("disabled", true);
                                            $('#default_lang').prop("checked", true);
                                        });
                                        function editUser(id) {
                                            $.ajax({
                                                type: "POST",
                                                url: "<?= base_url('getuserinfo') ?>",
                                                data: {uid: id}
                                            }).done(function (data) {
                                                var info = JSON.parse(data);
                                                var projects = info.projects.split(",");
                                                var privileges = info.privileges.split(",");
                                                $('#addUser').modal('show');
                                                $("[name='update']").val(id);
                                                $("[name='username']").val(info.username);
                                                $("[name='username']").prop('disabled', true);
                                                $("#GeneratePwd").text(lang.generate_new_pass);
                                                $("[name='fullname']").val(info.fullname);
                                                $("#cover").attr('src', '<?= base_url() . $GLOBALS['CONFIG']['IMAGESUSERSUPLOADDIR'] ?>' + info.image);
                                                $("#cover").show();
                                                $("[name='facebook']").val(info.social.facebook);
                                                $("[name='twitter']").val(info.social.twitter);
                                                $("[name='linkedin']").val(info.social.linkedin);
                                                $("[name='skype']").val(info.social.skype);
                                                $("[name='email']").val(info.email);
                                                if (info.lang != null) {
                                                    $("[name='lang']").prop("disabled", false);
                                                    $('#default_lang').prop("checked", false);
                                                    $("[name='lang'] option").filter(function () {
                                                        return $(this).text() == info.lang;
                                                    }).prop('selected', true);
                                                }
                                                $("#prof-list option").filter(function () {
                                                    return $(this).text() == info.profession_name;
                                                }).prop('selected', true);
                                                $(".proj-checkbox").filter(function () {
                                                    return info.projects.contains(this.value);
                                                }).prop('checked', true);
                                                var priv_cust = true;
                                                $("#privileges option").filter(function () {
                                                    if (this.value == info.privileges) {
                                                        priv_cust = false;
                                                    }
                                                    return this.value == info.privileges;
                                                }).prop('selected', true);
                                                if (priv_cust == true) {
                                                    $("#custom-privileges-cancel, #privileges-types").show();
                                                    $("#privileges, #custom-privileges").hide();
                                                    $("[name='costom-privileges']").val(1);
                                                    $(".usrtype-checkbox").filter(function () {
                                                        return info.privileges.contains(this.value);
                                                    }).prop('checked', true);
                                                }
                                            });
                                        }

                    </script>
                    <?php
                } elseif (url_segment(1) !== false && url_segment(1) == 'projects') {
                    if (isset($_GET['delete_project'])) {
                        $this->deleteProject($_GET['delete_project']);
                        redirect('projects');
                    }
                    $projects = $this->getProjects(null, true);
                    $sync_connections = $this->getSyncInfo();
                    ?>
                    <h1><?= $this->lang_php['projects'] ?>:</h1>
                    <hr>
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#createModal">
                        <?= $this->lang_php['add_project'] ?>
                    </button>
                    <hr>
                    <div id="result-ajax"></div>
                    <?php if (!empty($projects)) { ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?= $this->lang_php['abbrevation'] ?></th>
                                        <th><?= $this->lang_php['project_name'] ?></th>
                                        <th><?= $this->lang_php['sync_email_account'] ?></th>
                                        <th><?= $this->lang_php['time_created'] ?></th>
                                        <th><?= $this->lang_php['num_of_tickets'] ?></th>
                                        <th><?= $this->lang_php['users'] ?></th>
                                        <th><?= $this->lang_php['activity_log_tickets'] ?></th>
                                        <th class="text-center"><?= $this->lang_php['remove'] ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $proj) { ?>
                                        <tr>
                                            <td>
                                                <span class="the-abbr-<?= $proj['id'] ?>"><?= $proj['abbr'] ?></span>
                                                <input type="text" value="<?= $proj['abbr'] ?>" class="input-abbr-<?= $proj['id'] ?>" style="display:none;"> 
                                                <a href="javascript:void(0);" id="edit-abbr-<?= $proj['id'] ?>" onclick="editAbbr(<?= $proj['id'] ?>)" class="pull-right"><span class="glyphicon glyphicon-pencil"></span></a>
                                                <a href="javascript:void(0);" style="display:none;" id="save-edit-abbr-<?= $proj['id'] ?>" onclick="saveEditAbbr(<?= $proj['id'] ?>)" class="pull-right"><span class="glyphicon glyphicon-floppy-disk"></span></a>
                                            </td>
                                            <td>
                                                <span class="the-name-<?= $proj['id'] ?>"><?= $proj['name'] ?></span>
                                                <input type="text" value="<?= $proj['name'] ?>" class="input-name-<?= $proj['id'] ?>" style="display:none;"> 
                                                <a href="javascript:void(0);" id="edit-name-<?= $proj['id'] ?>" onclick="editName(<?= $proj['id'] ?>)" class="pull-right"><span class="glyphicon glyphicon-pencil"></span></a>
                                                <a href="javascript:void(0);" style="display:none;" id="save-edit-name-<?= $proj['id'] ?>" onclick="saveEditName(<?= $proj['id'] ?>)" class="pull-right"><span class="glyphicon glyphicon-floppy-disk"></span></a>
                                            </td>
                                            <td>
                                                <select class="form-control" data-change="<?= $proj['id'] ?>" onchange="editSync(<?= $proj['id'] ?>)">
                                                    <option value="0">none</option>
                                                    <?php foreach ($sync_connections as $conn) { ?>
                                                        <option <?= $conn['id'] == $proj['sync'] ? 'selected' : '' ?> value="<?= $conn['id'] ?>"><?= $conn['hostname'] ?> - <?= $conn['username'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><?= date(PROJECTS_TIME_CREATED, $proj['timestamp']) ?></td>
                                            <td><a href="<?= base_url('tickets/' . $proj['name'] . '/dashboard') ?>"><?= $this->lang_php['open_dash'] ?> (<?= $proj['num_tickets'] < MAX_NUM_NOTIF ? $proj['num_tickets'] : MAX_NUM_IN_BRECKETS . '+' ?>)</a></td>
                                            <td><a href="<?= base_url('settings/profiles?for_proj=' . $proj['id']) ?>"><?= $this->lang_php['see_list'] ?> (<?= $proj['num_users'] < MAX_NUM_NOTIF ? $proj['num_users'] : MAX_NUM_IN_BRECKETS . '+' ?>)</a></td>
                                            <td><a href="<?= base_url('settings#settings-activ-tab') ?>"><?= $this->lang_php['see_activity_log'] ?> (<?= $proj['num_logs_tickets'] < MAX_NUM_NOTIF ? $proj['num_logs_tickets'] : MAX_NUM_IN_BRECKETS . '+' ?>)</a></td>
                                            <td class="bg-danger text-center"><a href="<?= base_url('settings/projects?delete_project=' . $proj['id']) ?>" onclick="return confirm('<?= $this->lang_php['deleting_project'] ?>')"><span class="glyphicon glyphicon-remove"></span></a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else {
                        ?>
                        <div class="alert alert-danger"><?= $this->lang_php['no_projects'] ?></div>
                        <?php
                    }
                    ?>
                    <!-- ModalAddProject -->
                    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['add_new_project'] ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div id="result"></div>
                                    <input type="hidden" value="0" name="update">
                                    <div class="form-group">
                                        <label for="title"><?= $this->lang_php['name'] ?>:</label>
                                        <input type="text" class="form-control" name="title" placeholder="<?= $this->lang_php['name_regex'] ?>" value="" id="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="abbr"><?= $this->lang_php['abbrevation'] ?>:</label>
                                        <input type="text" class="form-control" name="abbr" placeholder="<?= $this->lang_php['abbrevation_regex'] ?>" maxlength="3" value="" id="abbr">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                                    <button type="button" class="btn btn-primary" onclick="setProject('../add_project')"><?= $this->lang_php['create'] ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="<?= base_url('assets/js/project_add.js') ?>"></script>
                    <script>
                                        function editAbbr(id) {
                                            $(".the-abbr-" + id + ", #edit-abbr-" + id).hide();
                                            $(".input-abbr-" + id + ", #save-edit-abbr-" + id).show();
                                        }
                                        function editName(id) {
                                            $(".the-name-" + id + ", #edit-name-" + id).hide();
                                            $(".input-name-" + id + ", #save-edit-name-" + id).show();
                                        }
                                        function saveEditName(id) {
                                            var sync_to = $('[data-change="' + id + '"]').val();
                                            var val_name = $(".input-name-" + id).val();
                                            var val_abbr = $(".input-abbr-" + id).val();
                                            $(".the-name-" + id).text(val_name);
                                            var res = goAjax(val_name, val_abbr, id);
                                            if (res == true) {
                                                $(".the-name-" + id + ", #edit-name-" + id).show();
                                                $(".input-name-" + id + ", #save-edit-name-" + id).hide();
                                            }
                                        }
                                        function saveEditAbbr(id) {
                                            var sync_to = $('[data-change="' + id + '"]').val();
                                            var val_name = $(".input-name-" + id).val();
                                            var val_abbr = $(".input-abbr-" + id).val();
                                            $(".the-abbr-" + id).text(val_abbr);
                                            var res = goAjax(val_name, val_abbr, id);
                                            if (res == true) {
                                                $(".the-abbr-" + id + ", #edit-abbr-" + id).show();
                                                $(".input-abbr-" + id + ", #save-edit-abbr-" + id).hide();
                                            }
                                        }
                                        function editSync(id) {
                                            var sure = confirm('Are you sure?');
                                            if (sure) {
                                                var sync_to = $('[data-change="' + id + '"]').val();
                                                var val_name = $(".input-name-" + id).val();
                                                var val_abbr = $(".input-abbr-" + id).val();
                                                goAjax(val_name, val_abbr, sync_to, id);
                                            }
                                        }
                                        function goAjax(name, abbr, sync_to, id) {
                                            var output;
                                            $("#loading").show();
                                            $.ajax({
                                                type: "POST",
                                                async: false,
                                                url: "<?= base_url('add_project') ?>",
                                                data: {name: name, abbr: abbr, sync_to: sync_to, update: id}
                                            }).done(function (result) {
                                                $("#loading").hide();
                                                $("#result-ajax").empty();
                                                $("#result-ajax").show();
                                                if (result > 0) {
                                                    $("#result-ajax").append('<div class="alert alert-success">' + lang.project_add_is + ' ' + lang.updated + ' ' + lang.project_add_succ + '</div>');
                                                    output = true;
                                                } else if (result == 0)
                                                {
                                                    $("#result-ajax").append('<div class="alert alert-danger">' + lang.problem_with_db + '</div>');
                                                } else {
                                                    $("#result-ajax").append('<div class="alert alert-danger">' + result + '</div>');
                                                }
                                                $("#result-ajax").delay(3000).fadeOut(1000);
                                            });
                                            return output;
                                        }
                    </script>
                    <div id="loading">Loading..</div>
                    <?php
                } elseif (url_segment(1) !== false && url_segment(1) == 'wiki_spaces') {
                    if (isset($_GET['delete_space'])) {
                        $this->deleteSpace($_GET['delete_space']);
                        $this->set_alert($this->lang_php['space_was_deleted'], 'success');
                        redirect('wiki_spaces');
                    }

                    if (isset($_POST['key_space']) || isset($_POST['update'])) {
                        $img_name = $this->uploadImage($GLOBALS['CONFIG']['IMAGESSPACESUPLOADDIR']);
                        $_POST['image'] = $img_name;
                        $result = $this->setSpace($_POST);
                        if ($result == true) {
                            $this->set_alert($this->lang_php['space_was_created'], 'success');
                            redirect('wiki_spaces');
                        }
                    }

                    $search = isset($_GET['project-filter']) ? $_GET['project-filter'] : null;
                    $num_results = $this->getCountSpaces($search);
                    $num_pages = ceil($num_results / RESULT_LIMIT_SETTINGS_SPACES);
                    $pg = isset($_GET['pg']) ? intval($_GET['pg']) : 0;
                    $pg = min($pg, $num_pages);
                    $pg = max($pg, 0);
                    $from = RESULT_LIMIT_SETTINGS_SPACES * ($pg);

                    $spaces = $this->getSpaces(null, null, $from, RESULT_LIMIT_SETTINGS_SPACES, $search);
                    echo $this->get_alert();
                    ?>
                    <h1><?= $this->lang_php['wiki_spaces'] ?>:</h1>
                    <hr>
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#addSpace">
                        <?= $this->lang_php['add_space'] ?>
                    </button>
                    <hr>
                    <form method="GET" id="goSelect" action="">
                        <select name="project-filter" data-style="btn-primary" class="selectpicker show-tick show-menu-arrow" id="filter_space" title="Filter by project">
                            <option value=""><?= $this->lang_php['none'] ?></option>
                            <option data-divider="true"></option>
                            <?php foreach ($projects_list as $proj) { ?>
                                <option <?= isset($_GET['project-filter']) && $_GET['project-filter'] == $proj['id'] ? 'selected' : '' ?> value="<?= $proj['id'] ?>"><?= $proj['name'] ?></option>
                            <?php } ?>
                        </select>
                    </form>
                    <hr>
                    <div id="result-ajax"></div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?= $this->lang_php['name'] ?></th>
                                    <th><?= $this->lang_php['space_key'] ?></th>
                                    <th><?= $this->lang_php['description'] ?></th>
                                    <th><?= $this->lang_php['image'] ?></th>
                                    <th><?= $this->lang_php['pages'] ?></th>
                                    <th><?= $this->lang_php['project'] ?></th>
                                    <th><?= $this->lang_php['time_created'] ?></th>
                                    <th class="text-center"><?= $this->lang_php['edit'] ?></th>
                                    <th class="text-center"><?= $this->lang_php['remove'] ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($spaces as $space) { ?>
                                    <tr>
                                        <td>
                                            <?= $space['name'] ?>
                                        </td>
                                        <td>
                                            <?= $space['key_space'] ?>
                                        </td>
                                        <td>
                                            <?= word_limiter($space['description'], 400) ?>
                                        </td>
                                        <td>
                                            <img class="space-logo-img" src="<?= base_url(returnSpaceImageUrl($space['image'])) ?>">
                                        </td>
                                        <td>
                                            <a href="<?= base_url('wiki/' . $space['proj_name'] . '/display/' . $space['key_space']) ?>"><?= $space['num_pages'] ?></a>
                                        </td>
                                        <td><?= $space['proj_name'] ?></td>
                                        <td><?= date(SPACES_TIME_CREATED, $space['timestamp']) ?></td>
                                        <td class="bg-info text-center"><a href="javascript:void(0)" onclick="editSpace(<?= $space['id'] ?>)"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                        <td class="bg-danger text-center"><a href="<?= base_url('settings/wiki_spaces?delete_space=' . $space['id']) ?>" onclick="return confirm('<?= $this->lang_php['space_deleting'] ?>')"><span class="glyphicon glyphicon-remove"></span></a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (isset($_GET['project-filter']) && $_GET['project-filter'] != '') {
                        $query_string = '&project-filter=' . $_GET['project-filter'];
                    } else {
                        $query_string = '';
                    }
                    ?>
                    <?= paging('settings/wiki_spaces', $query_string, $num_results, $pg, RESULT_LIMIT_SETTINGS_SPACES) ?>
                    <!-- ModalAddSpace -->
                    <div class="modal fade" id="addSpace" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <form class="form-horizontal" role="form" method="post" id="setSpace" action="" enctype="multipart/form-data">
                                <input type="hidden" name="update" value="0">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['add_new_space'] ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-danger" id="sp-result"></div>
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label class="control-label" for="name"><?= $this->lang_php['name'] ?> *</label>
                                                <input type="text" class="form-control" id="name" value="" name="name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label class="control-label" for="sp_key"><?= $this->lang_php['space_key'] ?> *</label>
                                                <input type="text" class="form-control" id="sp_key" value="" name="key_space">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label class="control-label" for="description"><?= $this->lang_php['description'] ?></label>
                                                <textarea name="description" id="description" rows="50" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <img src="" alt="none" style="display:none;" id="sp-cover">
                                            <div class="col-xs-12">
                                                <label class="control-label"><?= $this->lang_php['image'] ?></label>
                                                <input type="file"  name="image" id="fileToUpload">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label class="control-label"><?= $this->lang_php['select_project'] ?>:</label>
                                                <select name="project_id" class="form-control">
                                                    <?php foreach ($projects_list as $proj) { ?>
                                                        <option value="<?= $proj['id'] ?>"><?= $proj['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                                        <button type="button" class="btn btn-primary" name="setspace" onclick="validateSpace()"><?= $this->lang_php['create'] ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <script>
                        $("#filter_space").change(function () {
                            document.getElementById("goSelect").submit();
                        });
                        function validateSpace() {
                            var name = $("#name").val();
                            var space_key = $("#sp_key").val();
                            var is_update = $('[name="update"]').val();
                            var errors = new Array();
                            if (jQuery.trim(name).length <= 0 || jQuery.trim(name).length > 50) {
                                errors[0] = '<strong>' + lang.space_name_err + '</strong> ' + lang.space_name_valid;
                            }
                            if (is_update == 0) {
                                if (jQuery.trim(space_key).length > 20 || jQuery.trim(space_key).length < 3 || jQuery.trim(space_key).search(<?= $GLOBALS['CONFIG']['SPACEKEYREGEX'] ?>) === -1) {
                                    errors[1] = '<strong>' + lang.space_key_wrong + '</strong> ' + lang.space_key_allowed;
                                } else {
                                    var taken;
                                    $.ajax({
                                        type: "POST",
                                        async: false,
                                        url: "<?= base_url('space_key_check') ?>",
                                        data: {space_key: jQuery.trim(space_key)}
                                    }).done(function (data) {
                                        if (data > 0) {
                                            taken = true;
                                        }
                                    });
                                    if (taken == true) {
                                        errors[1] = '<strong>' + lang.space_taken + '</strong> ' + lang.space_select_another;
                                    }
                                }
                            }
                            if (errors.length > 0) {
                                $("#sp-result").show();
                                for (i = 0; i < errors.length; i++) {
                                    if (i == 0)
                                        $("#sp-result").empty();
                                    if (typeof errors[i] !== 'undefined') {
                                        $("#sp-result").append(errors[i] + "<br>");
                                    }
                                }
                                $('.modal-open .modal').scrollTop(0);
                            } else {
                                document.getElementById("setSpace").submit();
                            }
                        }

                        function editSpace(id) {
                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('getspaceinfo') ?>",
                                data: {sid: id}
                            }).done(function (data) {
                                var info = JSON.parse(data);
                                $('#addSpace').modal('show');
                                $("#name").val(info.name);
                                $("#sp_key").prop('disabled', true);
                                $('[name="update"]').val(info.id);
                                $("#description").val(info.description);
                                $("#sp-cover").attr('src', '<?= base_url() . $GLOBALS['CONFIG']['IMAGESSPACESUPLOADDIR'] ?>' + info.image).show();
                                $('[name="project_id"] option').filter(function () {
                                    if ($(this).text() == info.proj_name) {
                                        return true;
                                    }
                                }).prop('selected', true);
                            });
                        }
                        $('#addSpace').on('show.bs.modal', function () {
                            load_cke();
                        });
                        $('#addSpace').on('hidden.bs.modal', function () {
                            document.getElementById("setSpace").reset();
                            $("#sp-cover").attr('src', '').hide();
                            $('[name="update"]').val(0);
                            $("#sp_key").prop('disabled', false);
                        });
                    </script>
                    <?php
                } elseif (url_segment(1) !== false && url_segment(1) == 'wiki_templates') {
                    if (isset($_POST['addtempl'])) {
                        $result = $this->setPageTemplate($_POST);
                        if ($result == true) {
                            $this->set_alert($this->lang_php['template_was_saved'] . '!', 'success');
                        } else {
                            $this->set_alert($this->lang_php['template_save_err'] . '!', 'danger');
                        }
                        redirect('wiki_templates');
                    }

                    if (isset($_GET['delete_tmpl'])) {
                        $this->deteleTemplate($_GET['delete_tmpl']);
                        $this->set_alert($this->lang_php['template_deleted'] . '!', 'success');
                        redirect('wiki_templates');
                    }

                    $templates = $this->getPageTemplates(1);
                    echo $this->get_alert();
                    ?>
                    <h1><?= $this->lang_php['wiki_templates'] ?>:</h1>
                    <hr>
                    <button class="btn btn-primary btn-lg" data-target="#addTemplate" data-toggle="modal" type="button"> <?= $this->lang_php['add_template'] ?> </button>
                    <hr>
                    <?php if (!empty($templates)) { ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $this->lang_php['name'] ?></th>
                                        <th><?= $this->lang_php['content'] ?></th>
                                        <th><?= $this->lang_php['edit'] ?></th>
                                        <th><?= $this->lang_php['delete'] ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($templates as $tmpl) { ?>
                                        <tr>
                                            <td><?= $tmpl['id'] ?></td>
                                            <td><?= $tmpl['name'] ?></td>
                                            <td><?= word_limiter(htmlspecialchars($tmpl['content']), 100) ?></td>
                                            <td class="bg-info text-center"><a href="javascript:void(0)" onclick="editTemplate(<?= $tmpl['id'] ?>)"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                            <td class="bg-danger text-center"><a href="<?= base_url('settings/wiki_templates?delete_tmpl=' . $tmpl['id']) ?>" onclick="return confirm('<?= $this->lang_php['delete_confirm'] ?>')"><span class="glyphicon glyphicon-remove"></span></a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger"><?= $this->lang_php['no_custom_templates'] ?>!</div>             
                    <?php } ?>

                    <!-- Modal -->
                    <div class="modal fade" id="addTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <form action="" method="POST">
                                <input type="hidden" value="0" name="update">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['add_edit_templates'] ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label class="control-label" for="name"><?= $this->lang_php['name'] ?> *</label>
                                                <input type="text" class="form-control" id="name" value="" name="name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <label class="control-label" for="description"><?= $this->lang_php['description'] ?></label>
                                                <textarea name="description" id="description" rows="50" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                                        <input type="submit" class="btn btn-primary" value="<?= $this->lang_php['save'] ?>" name="addtempl">
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        $("[name='addtempl']").click(function () {
                            if (jQuery.trim($("#name").val()).length <= 0) {
                                return false;
                            }
                        });
                        $('#addTemplate').on('show.bs.modal', function () {
                            load_cke();
                        });
                        $('#addTemplate').on('hidden.bs.modal', function () {
                            $("#name").val('');
                            $('[name="update"]').val(0);
                            $("#description").val('');
                        });
                        function editTemplate(id) {
                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('gettemplateinfo') ?>",
                                data: {tid: id}
                            }).done(function (data) {
                                var info = JSON.parse(data);
                                $('#addTemplate').modal('show');
                                $("#name").val(info.name);
                                $('[name="update"]').val(info.id);
                                $("#description").val(info.content);
                            });
                        }
                    </script>
                    <?php
                } elseif (url_segment(1) !== false && url_segment(1) == 'syncing') {
                    $emails = $this->getSyncInfo();
                    if (isset($_POST['hostname'])) {
                        if (!isset($_POST['_ssl']))
                            $_POST['_ssl'] = 0;
                        else
                            $_POST['_ssl'] = 1;
                        if (!isset($_POST['smtp_ssl']))
                            $_POST['smtp_ssl'] = 0;
                        else
                            $_POST['smtp_ssl'] = 1;
                        if (!isset($_POST['self_signed_cert']))
                            $_POST['self_signed'] = 0;
                        else
                            $_POST['self_signed'] = 1;
                        $update = $_POST['update'];
                        unset($_POST['update'], $_POST['self_signed_cert']);
                        $this->setSync($_POST, $update);
                        $this->set_alert($this->lang_php['add_success'] . '!', 'success');
                        redirect('syncing');
                    }
                    if (isset($_GET['delete'])) {
                        $this->deleteSync((int) $_GET['delete']);
                        $this->set_alert($this->lang_php['delete_success'] . '!', 'danger');
                        redirect('syncing');
                    }
                    echo $this->get_alert();
                    ?>
                    <script src="<?= base_url('assets/js/function.setPort.js') ?>"></script>
                    <h1><?= $this->lang_php['emails_syncing'] ?>:</h1>
                    <hr>
                    <div class="panel panel-default panel-table">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col col-xs-6">
                                    <h3 class="panel-title"><?= $this->lang_php['email_boxes'] ?></h3>
                                </div>
                                <div class="col col-xs-6 text-right">
                                    <button type="button"  data-toggle="modal" data-target="#myModalCreateSync" class="btn btn-sm btn-primary btn-create"><?= $this->lang_php['create'] ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-list">
                                    <thead>
                                        <tr>
                                            <th><em class="fa fa-cog"></em></th>
                                            <th class="hidden-xs">ID</th>
                                            <th><?= $this->lang_php['hostname'] ?>(imap/pop3)</th>
                                            <th><?= $this->lang_php['hostname'] ?>(smtp)</th>
                                            <th><?= $this->lang_php['protocol'] ?></th>
                                            <th><?= $this->lang_php['port'] ?>(imap/pop3)</th>
                                            <th><?= $this->lang_php['port'] ?>(smtp)</th>
                                            <th><?= $this->lang_php['ssl'] ?>(imap/pop3)</th>
                                            <th><?= $this->lang_php['ssl'] ?>(smtp)</th>
                                            <th><?= $this->lang_php['self_signed_cert'] ?></th>
                                            <th><?= $this->lang_php['folder'] ?></th>
                                            <th><?= $this->lang_php['username'] ?></th>
                                            <th><?= $this->lang_php['pass'] ?></th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($emails !== false) {
                                            foreach ($emails as $email) {
                                                ?>
                                                <tr>
                                                    <td align="center">
                                                        <a class="btn btn-default" onclick="getUpdateInfo(<?= $email['id'] ?>)" data-toggle="modal" data-target="#myModalCreateSync"><em class="fa fa-pencil"></em></a>
                                                        <a class="btn btn-danger" href="?delete=<?= $email['id'] ?>" onclick="return confirm('<?= $this->lang_php['delete_confirm'] ?>')"><em class="fa fa-trash"></em></a>
                                                        <a class="btn btn-info" onclick="checkConnection(<?= $email['id'] ?>)" data-check-conn="<?= $email['id'] ?>"><?= $this->lang_php['check'] ?> <i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                    </td>
                                                    <td><?= $email['id'] ?></td>
                                                    <td data-hostname="<?= $email['id'] ?>"><?= $email['hostname'] ?></td>
                                                    <td data-smtp-hostname="<?= $email['id'] ?>"><?= $email['smtp_hostname'] ?></td>
                                                    <td data-protocol="<?= $email['id'] ?>"><?= $email['protocol'] ?></td>
                                                    <td data-port="<?= $email['id'] ?>"><?= $email['port'] ?></td>
                                                    <td data-smtp-port="<?= $email['id'] ?>"><?= $email['smtp_port'] ?></td>
                                                    <td data-ssl="<?= $email['id'] ?>" data-ssl-status="<?= $email['_ssl'] ?>"><?= $email['_ssl'] == 1 ? $this->lang_php['yes'] : $this->lang_php['no'] ?></td>
                                                    <td data-smtp-ssl="<?= $email['id'] ?>" data-smtp-ssl-status="<?= $email['smtp_ssl'] ?>"><?= $email['smtp_ssl'] == 1 ? $this->lang_php['yes'] : $this->lang_php['no'] ?></td>
                                                    <td data-selfsigned="<?= $email['id'] ?>" data-cert-status="<?= $email['self_signed'] ?>"><?= $email['self_signed'] == 1 ? $this->lang_php['yes'] : $this->lang_php['no'] ?></td>
                                                    <td data-folder="<?= $email['id'] ?>"><?= $email['folder'] ?></td>
                                                    <td data-username="<?= $email['id'] ?>"><?= $email['username'] ?></td>
                                                    <td data-password="<?= $email['id'] ?>"><?= $email['password'] ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="13"><?= $this->lang_php['none'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <a href="http://php.net/manual/en/book.imap.php" target="_blank"><img src="<?= base_url('assets/imgs/php_logo.png') ?>"></a> - IMAP, POP3 and NNTP | <a href="https://github.com/PHPMailer/PHPMailer" target="_blank"><img src="<?= base_url('assets/imgs/phpmailer_logo.png') ?>"></a>
                        </div>
                    </div>
                    <div id="err_info" class="alert alert-danger"></div>
                    <!-- Modal Edit/Create Emails -->
                    <div class="modal fade" id="myModalCreateSync" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['create'] ?></h4>
                                </div>
                                <div class="modal-body">
                                    <form role="form" method="POST" id="addEmailSync" action="">
                                        <input type="hidden" name="update" value="0">
                                        <div class="form-group">
                                            <label for="hostname"><?= $this->lang_php['hostname'] ?>:</label>
                                            <input type="text" class="form-control" name="hostname" placeholder="<?= $this->lang_php['example'] ?>: imap.gmail.com" value="" id="hostname">
                                        </div>
                                        <div class="form-group">
                                            <label for="smtp_hostname">SMTP <?= $this->lang_php['hostname'] ?>:</label>
                                            <input type="text" class="form-control" name="smtp_hostname" placeholder="<?= $this->lang_php['example'] ?>: smtp.gmail.com" value="" id="smtp_hostname">
                                        </div>
                                        <div class="form-group">
                                            <label for="protocol"><?= $this->lang_php['protocol'] ?>:</label>
                                            <select class="form-control" name="protocol" onchange="setPort('imap')" id="protocol">
                                                <option value="imap">imap</option>
                                                <option value="pop3">pop3</option>
                                                <option value="nntp">nntp</option>
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" onclick="setPort('imap')" name="_ssl" value="1"><?= $this->lang_php['ssl'] ?></label>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" onclick="setPort('ssl')" name="smtp_ssl" value="1"><?= $this->lang_php['ssl'] ?>(smtp)</label>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="self_signed_cert" value=""><?= $this->lang_php['self_signed_cert'] ?></label>
                                        </div>
                                        <div class="form-group">
                                            <label for="port"><?= $this->lang_php['port'] ?>:</label>
                                            <input type="text" class="form-control" name="port" placeholder="<?= $this->lang_php['example'] ?>: 993" value="143" id="port">
                                        </div>
                                        <div class="form-group">
                                            <label for="smtp_port">SMTP <?= $this->lang_php['port'] ?>:</label>
                                            <input type="text" class="form-control" name="smtp_port" placeholder="<?= $this->lang_php['example'] ?>: 587" value="587" id="smtp_port">
                                        </div>
                                        <div class="form-group">
                                            <label for="folder"><?= $this->lang_php['folder'] ?>:</label>
                                            <input type="text" class="form-control" name="folder" placeholder="<?= $this->lang_php['example'] ?>: INBOX" value="" id="folder">
                                        </div>
                                        <div class="form-group">
                                            <label for="uname_h"><?= $this->lang_php['username'] ?>:</label>
                                            <input type="text" class="form-control" name="username" placeholder="<?= $this->lang_php['example'] ?>: kiril@gmail.com" value="" id="uname_h">
                                        </div>
                                        <div class="form-group">
                                            <label for="pass_h"><?= $this->lang_php['pass'] ?>:</label>
                                            <input type="text" class="form-control" name="password" placeholder="<?= $this->lang_php['example'] ?>: <?= $this->lang_php['pass'] ?>" value="" id="pass_h">
                                        </div>
                                    </form>
                                    <div class="alert alert-danger" id="check-errors"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                                    <button type="button" class="btn btn-primary" id="save"><?= $this->lang_php['save'] ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $('#save').click(function () {
                            var hostname = $('[name="hostname"]').val();
                            var smtp_hostname = $('[name="smtp_hostname"]').val();
                            var protocol = $('#protocol').val();
                            var _ssl = 0;
                            var smtp_ssl = 0;
                            var self_signed_cert = 0;
                            if ($('[name="_ssl"]').is(':checked')) {
                                _ssl = 1
                            }
                            if ($('[name="smtp_ssl"]').is(':checked')) {
                                smtp_ssl = 1
                            }
                            if ($('[name="self_signed_cert"]').is(':checked')) {
                                self_signed_cert = 1
                            }
                            var port = $('[name="port"]').val();
                            var smtp_port = $('[name="smtp_port"]').val();
                            var folder = $('[name="folder"]').val();
                            var username = $('[name="username"]').val();
                            var password = $('[name="password"]').val();
                            var errors = new Array();
                            if ($.trim(hostname) == '') {
                                errors[0] = '<strong>' + lang.hostname + '</strong> ' + lang.required;
                            }
                            if ($.trim(smtp_hostname) == '') {
                                errors[1] = '<strong>' + lang.hostname + '(smtp)</strong> ' + lang.required;
                            }
                            if ($.trim(port) == '') {
                                errors[2] = '<strong>' + lang.port + '</strong> ' + lang.required;
                            }
                            if ($.trim(smtp_port) == '') {
                                errors[3] = '<strong>' + lang.port + '(smtp)</strong> ' + lang.required;
                            }
                            if ($.trim(folder) == '') {
                                errors[4] = '<strong>' + lang.folder + '</strong> ' + lang.required;
                            }
                            if ($.trim(username) == '') {
                                errors[5] = '<strong>' + lang.username + '</strong> ' + lang.required;
                            }
                            if ($.trim(password) == '') {
                                errors[6] = '<strong>' + lang.password + '</strong> ' + lang.required;
                            }
                            if (errors.length > 0) {
                                $("#check-errors").empty().show();
                                for (i = 0; i < errors.length; i++) {
                                    if (typeof errors[i] !== 'undefined') {
                                        $("#check-errors").append(errors[i] + "<br>");
                                    }
                                }
                            } else {
                                document.getElementById("addEmailSync").submit();
                            }
                        });
                        $('#myModalCreateSync').on('hidden.bs.modal', function () {
                            $("[name='update']").val('0');
                            $('[name="_ssl"]').attr('checked', false);
                            $('[name="smtp_ssl"]').attr('checked', false);
                            $('[name="self_signed_cert"]').attr('checked', false);
                            document.getElementById("addEmailSync").reset();
                        });
                        function getUpdateInfo(id) {
                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('getsyncemailinfo') ?>",
                                data: {eid: id}
                            }).done(function (data) {
                                var info = JSON.parse(data);
                                $("[name='update']").val(id);
                                $("[name='hostname']").val(info.hostname);
                                $("[name='smtp_hostname']").val(info.smtp_hostname);
                                $("#protocol option").each(function () {
                                    if ($(this).val() == info.protocol) {
                                        $(this).attr('selected', true);
                                    }
                                });
                                $('[name="port"]').val(info.port);
                                $('[name="smtp_port"]').val(info.smtp_port);
                                if (info._ssl == 0) {
                                    $('[name="_ssl"]').attr('checked', false);
                                } else {
                                    $('[name="_ssl"]').attr('checked', true);
                                }
                                if (info.smtp_ssl == 0) {
                                    $('[name="smtp_ssl"]').attr('checked', false);
                                } else {
                                    $('[name="smtp_ssl"]').attr('checked', true);
                                }
                                if (info.self_signed == 0) {
                                    $('[name="self_signed_cert"]').attr('checked', false);
                                } else {
                                    $('[name="self_signed_cert"]').attr('checked', true);
                                }
                                $('[name="folder"]').val(info.folder);
                                $('[name="username"]').val(info.username);
                                $('[name="password"]').val(info.password);
                            });
                        }
                        function checkConnection(id) {
                            var hostname = $('[data-hostname="' + id + '"]').text();
                            var smtp_hostname = $('[data-smtp-hostname="' + id + '"]').text();
                            var protocol = $('[data-protocol="' + id + '"]').text();
                            var port = $('[data-port="' + id + '"]').text();
                            var smtp_port = $('[data-smtp-port="' + id + '"]').text();
                            var _ssl = $('[data-ssl="' + id + '"]').attr('data-ssl-status');
                            var smtp_ssl = $('[data-smtp-ssl="' + id + '"]').attr('data-smtp-ssl-status');
                            var self_signed_cert = $('[data-selfsigned="' + id + '"]').attr('data-cert-status');
                            var folder = $('[data-folder="' + id + '"]').text();
                            var username = $('[data-username="' + id + '"]').text();
                            var password = $('[data-password="' + id + '"]').text();

                            $("#check-errors").hide();
                            $('[data-check-conn="' + id + '"] i.fa').remove();
                            $('[data-check-conn="' + id + '"]').append('<i class="fa fa-refresh fa-spin"></i>');
                            $('#err_info').hide();
                            $('#err_info').empty();
                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('sync_conn_check') ?>",
                                data: {hostname: hostname, smtp_hostname: smtp_hostname, protocol: protocol, _ssl: _ssl, smtp_ssl: smtp_ssl, self_signed_cert: self_signed_cert, port: port, smtp_port: smtp_port, folder: folder, username: username, password: password}
                            }).done(function (data) {
                                if (data == 1) {
                                    $('[data-check-conn="' + id + '"] i.fa').remove();
                                    $('[data-check-conn="' + id + '"]').append('<i class="fa fa-check" aria-hidden="true"></i>');
                                } else {
                                    $('[data-check-conn="' + id + '"] i.fa').remove();
                                    $('[data-check-conn="' + id + '"]').append('<i class="fa fa-times" aria-hidden="true"></i>');
                                    $('#err_info').show();
                                    $('#err_info').append(data);
                                }
                            });
                        }
                    </script>
                <?php } elseif (url_segment(1) !== false && url_segment(1) == 'logs') { ?>
                    <h1><?= $this->lang_php['logs'] ?>:</h1>
                    <hr>
                    <div class="table-responsive">
                        <?php
                        $have_log = file_exists("logs/errors.log");
                        if ($have_log != false) {
                            $myfile = fopen("logs/errors.log", "r");
                            ?>
                            <table class="table table-striped table-bordered table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>PHP <?= $this->lang_php['error'] ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while (($line = fgets($myfile)) !== false) {
                                        ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td class="danger"><?= $line ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info"><?= $this->lang_php['none'] ?></div>
                        <?php } ?>
                    </div>
                <?php }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $("#debugmode").addClass('pull-right');
        $("[data-toggle='tooltip']").tooltip();
        $('footer').remove();
    });
    $('[name="settings_search"]').keyup(function (e) {
        var search_q = $(this).val();
        if (jQuery.trim(search_q).length > 0) {
            $("#settings form.form-s img.s-loading").show();
            $.ajax({
                type: "POST",
                url: "<?= base_url('settings_search') ?>",
                data: {find: search_q}
            }).done(function (data) {
                if (data != 0) {
                    $("#settings-query-result").empty().show().append(data);
                } else {
                    $("#settings-query-result").empty().show().append('<a class="list-group-item select-suggestion" style="background-color:#f2dede !important;">' + lang.there_are_no_results + '</a>');
                }
                $("#settings form.form-s img.s-loading").hide();
            });
        } else {
            $("#settings-query-result").empty().hide();
        }
    });
    $(document).click(function (e) {
        $('#settings-query-result').empty().hide();
    });
    function load_cke() {
        if ($("#cke_description")) {
            $("#cke_description").remove();
        }
        var hEd = CKEDITOR.instances['description'];
        if (hEd) {
            CKEDITOR.remove(hEd);
        }
        CKEDITOR.replace('description');
    }
</script>

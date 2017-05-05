var cheerio = require('cheerio');
var request = require('request');
var mysql = require('mysql');
var http = require('http');
var fs = require('fs');

var connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'livescore'
});

connection.connect();

var list_doi_bong = [{
    "LeagueID": 1,
    "name": "Bóng đá anh",
    "flag": "http://img.kenhthethao.vn/2017/05/flag-Anh.png",
    "order_giai_dau": 0
    },
    {
        "LeagueID": 4,
        "name": "bóng đá tây ban nha",
        "flag": "http://img.kenhthethao.vn/2017/05/flag-tayBanNha.png",
        "order_giai_dau": 0
    },
    {
        "LeagueID": 6,
        "name": "Bóng đá pháp",
        "flag": "http://img.kenhthethao.vn/2017/05/flag-Phap.png",
        "order_giai_dau": 0
    },
    {
        "LeagueID": 5,
        "name": "Bóng đá đức",
        "flag": "http://img.kenhthethao.vn/2017/05/flag-Duc.png",
        "order_giai_dau": 0
    },
    {
        "LeagueID": 3,
        "name": "Bóng đá ý",
        "flag": "http://img.kenhthethao.vn/2017/05/flag-Y1.png",
        "order_giai_dau": 0
    },

    {
        "LeagueID": 2,
        "name": "Champion League",
        "flag": "http://img.kenhthethao.vn/2017/05/football_icon.png",
        "order_giai_dau": 0
    },
    {
        "LeagueID": 9,
        "name": "Euro Paleague",
        "flag": "http://img.kenhthethao.vn/2017/05/football_icon.png",
        "order_giai_dau": 0
    },
    {
        "LeagueID": 31,
        "name": "v-league",
        "flag": "http://img.kenhthethao.vn/2017/05/flag-Vietnam.png",
        "order_giai_dau": 0
    }];

function getKetQuaThiDau(url, callback) {
    request(url, function (err, res, body) {
        if (!err && res.statusCode == 200) {
            var $ = cheerio.load(body);
            $('.played_box .fixture_list tr.ls').each(function () {

                var id_tour = getParameterByName('LeagueID', url);

                var name_tour = $(this).children().children('a').text();
                var check = 0;
                var stt = 0;

                $('.played_box .fixture_list tr').each(function () {
                    if ($(this).attr('class') === 'ls') {
                        check = 1;
                        stt++;
                    }
                    if (check == 0 || stt == 1) {
                        if ($(this).attr('class') !== 'ls') {
                            var time_start = $(this).children().eq(0).children().text();
                            if (time_start.trim() == '') {
                                time_start = $(this).children().eq(0).text();
                            }
                            time_start = time_start.trim();
                            time_start = time_start.split(' ');
                            var date = new Date();
                            var time_start1 = time_start[0] + '/' + date.getFullYear() + ' ' + time_start[1] + ':00';
                            var time_start2 = time_start[0] + '/' + date.getFullYear();

                            var home = $(this).children().eq(1).children().text();
                            var score = $(this).children().eq(2).text();
                            score = score.split('-');
                            var guest = $(this).children().eq(3).children('a').text();

                            if (home.trim() != '' && guest.trim() != '') {
                                var metadata = {
                                    home_club_name: home,
                                    away_club_name: guest,
                                    home_goal: score[0],
                                    away_goal: score[1],
                                    is_postponed: 2,
                                    is_finish: 1,
                                    time_start: time_start1,
                                    LeagueID: id_tour,
                                    date_query: time_start2
                                }
                                var queryString = 'SELECT*FROM ketqua WHERE LeagueID = ' + id_tour + ' AND time_start = ' + '"' + time_start1 + '"' + ' AND home_club_name = ' + '"' + metadata.home_club_name + '"';
                                connection.query(queryString, function (err, rows, fields) {
                                    if (err) throw err;
                                    if (rows.length > 0) {
                                        connection.query('UPDATE ketqua SET ? WHERE LeagueID = ' + id_tour + ' AND time_start=' + '"' + time_start1 + '"' + ' AND home_club_name = ' + '"' + metadata.home_club_name + '"', metadata, function (error, result) {
                                            if (!error) {
                                                console.log('update ketqua success');
                                            } else {
                                                console.log(error);
                                            }
                                        });
                                    } else {
                                        connection.query('INSERT INTO ketqua SET ?', metadata, function (error, result) {
                                            if (!error) {
                                                console.log('insert ketqua success');
                                            } else {
                                                console.log(error);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    }
                });
            });
        } else {
            console.log(err);
        }
    });
}

function getLichThiDau(url, callback) {
    request(url, function (err, res, body) {
        if (!err && res.statusCode == 200) {
            var $ = cheerio.load(body);
            $('.coming_box .fx_coming tr.ls').each(function () {

                var id_tour = getParameterByName('LeagueID', url);

                var name_tour = $(this).children().children('a').text();
                var check = 0;

                $('.coming_box .fx_coming tr').each(function () {
                    if ($(this).attr('class') === 'ls') {
                        check = 1;
                    }
                    if (check == 1) {
                        if ($(this).attr('class') !== 'ls') {
                            var time_start = $(this).children().eq(0).children().text();
                            if (time_start.trim() == '') {
                                time_start = $(this).children().eq(0).text();
                            }
                            time_start = time_start.trim();
                            time_start = time_start.split(' ');
                            var date = new Date();
                            var time_start1 = time_start[0] + '/' + date.getFullYear() + ' ' + time_start[1] + ':00';
                            var time_start2 = time_start[0] + '/' + date.getFullYear();

                            var home = $(this).children().eq(1).children().text();

                            var match = home.split('-');
                            home = match[0];
                            home = home.trim();
                            guest = match[1];

                            if (typeof  home != 'undefined' && typeof  guest != 'undefined' && typeof time_start1 != 'undefined') {
                                var metadata = {
                                    home_club_name: home,
                                    away_club_name: guest,
                                    home_goal: 0,
                                    away_goal: 0,
                                    is_postponed: 2,
                                    time_start: time_start1,
                                    LeagueID: id_tour,
                                    date_query: time_start2
                                }
                                var queryString = 'SELECT*FROM ketqua WHERE LeagueID = ' + id_tour + ' AND time_start = ' + '"' + time_start1 + '"' + ' AND home_club_name = ' + '"' + metadata.home_club_name + '"';
                                connection.query(queryString, function (err, rows, fields) {
                                    if (err) throw err;
                                    if (rows.length > 0) {
                                        connection.query('UPDATE ketqua SET ? WHERE LeagueID = ' + id_tour + ' AND time_start=' + '"' + time_start1 + '"' + ' AND home_club_name = ' + '"' + metadata.home_club_name + '"', metadata, function (error, result) {
                                            if (!error) {
                                                console.log('update ketqua success');
                                            } else {
                                                console.log(error);
                                            }
                                        });
                                    } else {
                                        connection.query('INSERT INTO ketqua SET ?', metadata, function (error, result) {
                                            if (!error) {
                                                console.log('insert ketqua success');
                                            } else {
                                                console.log(error);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    }
                });
            });
        } else {
            console.log(err);
        }
    });
}

function Truncate() {
    connection.query('TRUNCATE ketqua', function (error, result) {
        if (!error) {
            console.log('insert success');
        } else {
            console.log(error);
        }
    });
}

// anh, tay ban nha, italia, duc, phap, champion legue, europa league, bong da viet nam
//var list_doi_bong = array(1,4,3,5,6,2,9,31);

function getListTeam(callback) {
    connection.query('TRUNCATE danh_sach_giai_dau', function (error, result) {
        if (!error) {
            console.log('truncate danh_sach_giai_dau success');
        } else {
            console.log(error);
        }
    });
    for (var $i = 0; $i < list_doi_bong.length; $i++) {
        connection.query('INSERT INTO danh_sach_giai_dau SET ?', list_doi_bong[$i], function (error, result) {
            if (!error) {
                console.log('insert danh_sach_giai_dau success');
            } else {
                console.log(error);
            }
        });
    }
}

function getBangXepHang(url, callback) {
    request(url, function (err, res, body) {
        if (!err && res.statusCode == 200) {
            var $ = cheerio.load(body);
            var season_time_name = $('.season_list table td[class="ctbl_selected"]').text();
            var season_time_name = season_time_name.replace("Mùa bóng", "");
            season_time_name = season_time_name.trim();
            var season_id = 0;
            var list_seasons = [];
            var table = [];
            var stt = 0;
            $('.standing_table table tr[align="right"]').each(function () {
                stt++;
                var football_club_name = $(this).children().eq(1).children('a').text();
                var total_match = $(this).children().eq(2).text();
                var point = $(this).children().eq(14).text();
                var total_win = parseInt($(this).children().eq(3).text()) + parseInt($(this).children().eq(8).text());
                var total_draw = parseInt($(this).children().eq(4).text()) + parseInt($(this).children().eq(9).text());
                var total_lose = parseInt($(this).children().eq(5).text()) + parseInt($(this).children().eq(10).text());
                var goal = $(this).children().eq(13).text();
                goal = goal.split('-');
                goal[0] = goal[0].trim();

                var metadata = {
                    id: stt,
                    football_club_name: football_club_name,
                    total_match: total_match,
                    point: point,
                    total_win: total_win,
                    total_draw: total_draw,
                    total_lose: total_lose,
                    goal: goal[0]
                }
                table.push(metadata);
            });
            table = JSON.stringify(table);

            var LeagueID = getParameterByName('LeagueID', url);

            var data = {
                seasion: season_time_name,
                LeagueID: LeagueID,
                data: table
            }

            var queryString = 'SELECT*FROM bang_xep_hang WHERE LeagueID = ' + LeagueID;
            connection.query(queryString, function (err, rows, fields) {
                if (err) throw err;
                if (rows.length > 0) {
                    connection.query('UPDATE bang_xep_hang SET ? WHERE LeagueID = ' + LeagueID, data, function (error, result) {
                        if (!error) {
                            console.log('update success');
                        } else {
                            console.log(error);
                        }
                    });
                } else {
                    connection.query('INSERT INTO bang_xep_hang SET ?', data, function (error, result) {
                        if (!error) {
                            console.log('insert success');
                        } else {
                            console.log(error);
                        }
                    });
                }
            });

        } else {
            console.log(err);
        }
    });
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

for (var $i = 0; $i < list_doi_bong.length; $i++) {
    var leagueID = list_doi_bong[$i].LeagueID;
    if (typeof leagueID != 'undefined') {
        getLichThiDau('http://bongdaso.com/_ComingMatches.aspx?LeagueID=' + leagueID + '&SeasonID=-1&Period=1&Odd=1');
        getKetQuaThiDau('http://bongdaso.com/_PlayedMatches.aspx?LeagueID=' + leagueID + '&SeasonID=-1&Period=1');
    }
}
getListTeam();
for (var $i = 0; $i < list_doi_bong.length; $i++) {
    var leagueID = list_doi_bong[$i].LeagueID;
    if (typeof leagueID != 'undefined') {
        getBangXepHang('http://bongdaso.com/Standing.aspx?LeagueID=' + leagueID + '&SeasonID=90');
    }
}




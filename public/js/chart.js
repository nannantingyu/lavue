var chart = {}, times = 1;
const info = {
    'btcusdt': {
        title: "比特币/USDT",
        name: "BTC/USDT"
    },
    'ethusdt': {
        title: "以太币/USDT",
        name: "ETH/USDT"
    }
};

$(function () {
    Highcharts.setOptions({
        global: {
            timezone: 'Asia/Shanghai'
        }
    });
});

const socket = io('https://image.yjshare.cn:8088', {
    secure: true,
    transports: ['websocket']
});

var addZero = function(dt) {
    return dt > 9?dt:'0'+parseInt(dt);
};

Date.prototype.toDtString = function (format) {
    return format.replace("%Y", addZero(this.getFullYear()))
        .replace("%M", addZero(this.getMonth()+1))
        .replace("%D", addZero(this.getDate()))
        .replace("%h", addZero(this.getHours()))
        .replace("%m", addZero(this.getMinutes()))
        .replace("%s", addZero(this.getSeconds()));
};

socket.on('connect', () => {
    console.log("connenct");
    socket.emit("kline_data", "btcusdt");
    socket.emit("tick_data", "btcusdt");

    socket.emit("kline_data", "ethusdt");
    socket.emit("tick_data", "ethusdt");

    socket.on('tick_client', function (data) {
        times ++;
        var char_this = chart[data.ch.split(".")[1]];
        if(char_this && times % 10 == 0) {
            var ori_data_1 = char_this.series[0].data, length = ori_data_1.length, lastdata_1 = ori_data_1[length-1],
                ori_data_2 = char_this.series[1].data, lastdata_2 = ori_data_2[length-1];

            var time = data.tick.id*1000;
            if(time == lastdata_1.x) {
                lastdata_1.update({
                    open: data.tick.open,
                    high: data.tick.high,
                    low: data.tick.low,
                    close: data.tick.close
                });

                lastdata_2.update({
                    y: data.tick.vol
                });
            }
            else {
                char_this.series[0].addPoint([
                        time,
                        data.tick.open,
                        data.tick.high,
                        data.tick.low,
                        data.tick.close]
                );
                char_this.series[1].addPoint([time, data.tick.vol]);
            }
        }
    });

    socket.on("history", function(data) {
        if(!data || data.length < 2){
            console.log("获取失败");
        }
        else {
            var ch = data.id;
            data = data.data;

            var ohlc = [],
                volume = [],
                dataLength = data.length,
                groupingUnits = [[
                    'week',                         // unit name
                    [1]                             // allowed multiples
                ], [
                    'month',
                    [1, 2, 3, 4, 6]
                ]],
                i = 0;
            for (i; i < dataLength; i += 1) {
                var time = new Date(data[i]['id']*1000), dt = time.toDtString("%h:%m");
                ohlc.push([
                    data[i]['id']*1000,
                    data[i]['open'], // open
                    data[i]['high'], // high
                    data[i]['low'], // low
                    data[i]['close'] // close
                ]);
                volume.push([
                    data[i]['id']*1000,
                    data[i]['vol'] // the volume
                ]);
            }

            chart[ch] = new Highcharts.StockChart({
                chart: {
                    renderTo: `container_${ch}`,
                },
                rangeSelector: {
                    selected: 1,
                    inputDateFormat: '%Y-%m-%d'
                },
                title: {
                    text: info[ch]['title']
                },
                xAxis: {
                    dateTimeLabelFormats: {
                        millisecond: '%H:%M:%S.%L',
                        second: '%H:%M:%S',
                        minute: '%H:%M',
                        hour: '%H:%M',
                        day: '%m-%d',
                        week: '%m-%d',
                        month: '%y-%m',
                        year: '%Y'
                    }
                },
                tooltip: {
                    split: false,
                    shared: true,
                },
                yAxis: [
                    {
                        labels: {
                            align: 'right',
                            x: -3
                        },
                        title: {
                            text: '价格'
                        },
                        height: '65%',
                        resize: {
                            enabled: true
                        },
                        lineWidth: 2
                    },
                    {
                        labels: {
                            align: 'right',
                            x: -3
                        },
                        title: {
                            text: '交易量'
                        },
                        top: '65%',
                        height: '35%',
                        offset: 0,
                        lineWidth: 2
                    }],
                series: [
                    {
                        type: 'candlestick',
                        name: info[ch]['name'],
                        color: 'green',
                        lineColor: 'green',
                        upColor: 'red',
                        upLineColor: 'red',
                        tooltip: {
                        },
                        navigatorOptions: {
                            color: Highcharts.getOptions().colors[0]
                        },
                        data: ohlc,
                        dataGrouping: {
                            units: groupingUnits
                        },
                        id: 'sz'
                    },
                    {
                        type: 'column',
                        data: volume,
                        yAxis: 1,
                        dataGrouping: {
                            units: groupingUnits
                        }
                    }
                ]
            });
        }
    });
});

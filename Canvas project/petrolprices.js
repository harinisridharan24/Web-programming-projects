// Drawing Graph displaying gas prices change in differnt cities of NY since Feb 16
// Created By Harini Sridharan

var gasPriceData = petrolData.data;
var gasPricesNewYork = [];

var canvas, context;

var canvasPositionArray = [];

var textCanvas, textContext;

// mapping option values to cities
var cityNames = {
    10: 'Albany',
    11: 'Bingamton',
    12: 'Buffalo',
    13: 'Nassau',
    14: 'New York City',
    15: 'Rochester',
    16: 'Syracuse',
    17: 'Utica'
}

// get city value selected in option
function getCityValue(){
    getGasPricesNY(document.getElementById("choose-city").value)
}

// get gas prices of selected city 
// (city): selected city value
function getGasPricesNY(city){

    if(typeof city === 'undefined' || city === null){
        city = 8;
    }
    gasPricesNewYork = [];
    for (var i = 0; i< 53; i++){
        gasPricesNewYork.push({'date': gasPriceData[i][8], 'price': gasPriceData[i][city]})
    }
    drawGraph(cityNames[city])
}

// draw the graph on canvas for selected city
// ciyName : name of the city that was selected
function drawGraph(cityName){
    var cityName = cityName
    
    // get the canvas
    canvas = document.getElementById('petrol-prices-graph');
    context = canvas.getContext('2d');
    // repaint the canvas
    canvas.width = canvas.width;

    // adding mouse move event listener to display the data point displayed while hovering the mouse over the circle i =  graph
    canvas.addEventListener('mousemove', function(evt) {
        var mousePos = getMousePos(canvas, evt);
        var message = 'Mouse position: ' + mousePos.x + ',' + mousePos.y;
        var box = document.getElementById("priceData");
        for(var k =0; k< canvasPositionArray.length; k++){
            // checking if the mouse position is within the circle.
            if((mousePos.x >= canvasPositionArray[k].x-5 && mousePos.x <= canvasPositionArray[k].x+5) && (mousePos.y >= canvasPositionArray[k].y-5 && mousePos.y <= canvasPositionArray[k].y+5)){
                box.style.display = 'block';
                // style of the box in which the data would be displayed.
                box.innerHTML =  "Avg weekly price of gas for week ending on " + canvasPositionArray[k].priceDate + ": $"+ canvasPositionArray[k].value;
                box.style.border = "1px solid #7cb5ec";
                box.style.background = "#ffffff";
                box.style.top = (canvasPositionArray[k].y + 20) + "px";
                box.style.left = (canvasPositionArray[k].x - 60 ) + "px";
                break;
            }
            else {
                box.style.display = 'none';
            }
        }
        

    }, false);


    drawXAxis(context);

    drawYAxis(context);

    plotPoints(context);

    writeChartDescription(context, cityName);

    writeXYAxisLabels(context);
}

// draw x axis on the graph populating the different month values.
// context - the context where the x axis labels have to be drawn

function drawXAxis(context){
    context.strokeStyle = "#666666";
    context.fillStyle = "#666666";


    context.beginPath();
    context.moveTo( 40, 0);
    context.lineTo( 40, 420);
    context.stroke();
    context.font="12px Arial";
    context.fillText(moment(gasPricesNewYork[52].date).format("MMM YY"), 45, 420);

    for (var i = 47; i>=5; i= i-5) {

        context.beginPath();
        context.moveTo( (14 * (53-i))+40, 400);
        context.lineTo( (14 * (53-i))+40, 420);
        context.stroke();
        context.fillText(moment(gasPricesNewYork[i].date).format("MMM YY"),14 * (53-i) + 45, 420);
    }
    context.beginPath();
    context.moveTo( (14 * 52)+40, 400);
    context.lineTo( (14 * 52)+40, 420);
    context.stroke();
    context.fillText(moment(gasPricesNewYork[0].date).format("MMM YY"),14 * 52 + 45, 420);
}


// draw y axis displaying different prices of gas in increasing order
// context - the context where the y axis labels have to be drawn
function drawYAxis(context){
    for ( var j = 100; j<=400; j = j+100){
        context.beginPath();
        context.moveTo( 40, j);
        context.lineTo( 1100, j);
        context.stroke();
        context.font="12px Arial";
        context.fillText((400-j)/100, 30,j);
    }
}

// plot the data points on the graph 
// context - the context where the data points have to be drawn
function plotPoints(context){
        var left = 45,
		prev_price = gasPricesNewYork[52].price * 100,
        // move each data point by 14
		move_left_by = 14;
        context.beginPath();
    for (var i = 52; i >=0; i--) {
        context.strokeStyle = '#7cb5ec'
        context.fillStyle = '#7cb5ec'
        // converting price to 100 point scale
        var currentPrice = gasPricesNewYork[i].price * 100

        // drawing the label from the top by subtracting the height from the price
        context.moveTo(left, 400-prev_price);
	    context.lineTo(left+move_left_by, 400-currentPrice);
        context.arc(left, 400-currentPrice, 5, 0, 2 * Math.PI);
        context.fillStyle = '#7cb5ec';
        context.fill();
        var datePrice = moment(gasPricesNewYork[i].date).format('DD-MM-YYYY');
        // creating canvas position array for where the data label is positioned in the canvas
        canvasPositionArray.push({x: left, y: 400-currentPrice, value: gasPricesNewYork[i].price, priceDate: datePrice })
        
        context.stroke();
        context.beginPath();
        // updating the canvas position
        prev_price = currentPrice;
        left += move_left_by;
    }
}

// updating the chart title
// context - the context where the x axis labels have to be drawn
// cityname : update the cityname for which the graph is drawn
function writeChartDescription(context, cityName){
    // write chart description
    context.beginPath();
    context.fillStyle = '#000000';
    context.font="20px Arial";
    context.fillText('Average weekly prices of gas in '+cityName+' since Feb 2016',200, 50);
}

// write the xy axis label 
// context - the context where the x axis labels have to be drawn
function writeXYAxisLabels(context){
    // draw x axis and y axis labels
     context.beginPath();
     context.fillStyle = '#000000';
     context.font="16px Arial";
     context.fillText('Months --->',400, 450);

     context.beginPath();
     context.translate( 840, 0 );
    // now rotate the canvas anti-clockwise by 270 degrees
    // holding onto the translate point
     context.rotate(3*Math.PI/2 );
     context.fillText('Prices in $  --->',-270, -820);
     context.restore();
}


// get mouse position while hovering the mouse in the canvas
// canvas - the canvas where the mouse is hovered
// event - event object 

function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}


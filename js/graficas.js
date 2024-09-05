"use strict";

// Shared Colors Definition
const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

function destruirGrafica(id){
	$('#'+id+'').children().remove();
}

var cargaGrafica = function () {
	// Private functions

	var barras = function (idGraficaBarras,barrasSeries,barrasCategorias,texto) {
		const apexChart = "#"+idGraficaBarras;
		var options = {
			series: barrasSeries,
			chart: {
				type: 'bar',
				height: 350
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '55%',
					endingShape: 'rounded'
				},
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent']
			},
			xaxis: {
				categories: barrasCategorias,
			},
			yaxis: {
				title: {
					text: texto
				}
			},
			fill: {
				opacity: 1
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return " " + val + " " +texto
					}
				}
			},
			colors: [primary, success, warning]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}
  	
	return {
		// public functions
		init: function (idGraficaBarras,barrasSeries,barrasCategorias,texto) {
            destruirGrafica(idGraficaBarras);
			barras(idGraficaBarras,barrasSeries,barrasCategorias,texto);
		}
	};
}();

/*jQuery(document).ready(function () {
	cargaGrafica.init("contador");
});*/
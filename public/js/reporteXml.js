new Vue({
	el:'#main',
	created:function(){
		this.getReporteXml();
	}
	data:{
		reporteXmls: []
	},
	methods:{
		getReporteXml:function(){
			alert('se esta ejecutando vue');
			var urlReporteXml='/regisretenciones/allxml';
			axios.get(urlReporteXml).then(response=>{
				this.reporteXml = response.data
			});
		}
	}
});
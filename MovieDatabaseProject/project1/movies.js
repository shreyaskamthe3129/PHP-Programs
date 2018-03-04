function initialize () {
}

function sendRequest () {
	
   var xhr = new XMLHttpRequest();
   var query = encodeURI(document.getElementById("form-input").value);
   var movieName = "<br/><br/>";
   xhr.open("GET", "proxy.php?method=/3/search/movie&query=" + query);
   xhr.setRequestHeader("Accept","application/json");
   
   xhr.onreadystatechange = function () {
	   console.log(this.readyState);
       if (this.readyState == 4) {
          var json = JSON.parse(this.responseText);
          var object = json.results;
          for(var counter in object) {
        	  movieName = movieName + "<a href='#' onclick='displayMovieInfo("+object[counter].id+")'>" + object[counter].original_title +"     ("+ object[counter].release_date + ")</a>" + "<br/>";
          }
          document.getElementById("output").innerHTML = movieName;
       }
   };
   xhr.send(null);
}

function displayMovieInfo (movieId) {
	
	var xhrObject = new XMLHttpRequest();
	var movieInfoString = "";
	xhrObject.open("GET","proxy.php?method=/3/movie/" + movieId);
	xhrObject.setRequestHeader("Accept","application/json");
	xhrObject.onreadystatechange = function () {
		if(this.readyState == 4) {
			var movieJsonObject = JSON.parse(this.responseText);
			movieInfoString = JSON.stringify(movieJsonObject,undefined,2);
			var moviePosterPath = movieJsonObject.poster_path;
			var movieTitle = movieJsonObject.original_title;
			var movieSummary = movieJsonObject.overview;
			var movieGenres = movieJsonObject.genres;
			var tempMovieGenresString = "";
			for(var counter in movieGenres) {
				tempMovieGenresString = tempMovieGenresString + movieGenres[counter].name + " ,";
			}
			var movieGenresString = tempMovieGenresString.substring(0,tempMovieGenresString.length-1);
		}
		var finalMovieInfo = "<img src='http://image.tmdb.org/t/p/w500/"+moviePosterPath+"' style='width:304px;height:228px;'></img>";
		finalMovieInfo = finalMovieInfo + "<br/>";
		finalMovieInfo = finalMovieInfo + "<h3>"+movieTitle+"</h3>";
		finalMovieInfo = finalMovieInfo + "<p>"+movieSummary+"</p>";
		finalMovieInfo = finalMovieInfo + "<h5><b>Genres : </b> "+movieGenresString+"</h5>";
		document.getElementById("movieInfo").innerHTML = finalMovieInfo;
	};
	displayCrewInfo(movieId);
	xhrObject.send(null);
}

function displayCrewInfo(movieId) {
	
	var xhrCrewObject = new XMLHttpRequest();
	var movieCrewString = "<h4><b>Cast :</b></h4>";
	xhrCrewObject.open("GET","proxy.php?method=/3/movie/" + movieId + "/credits");
	xhrCrewObject.setRequestHeader("Accept","application/json");
	xhrCrewObject.onreadystatechange = function () {
		if(this.readyState == 4) {
			var movieCrewObject = JSON.parse(this.responseText);
			var movieCastObject = movieCrewObject.cast;
			for(var counter=0;counter < 5; counter++) {
				if(movieCastObject[counter].character != null) {
					movieCrewString = movieCrewString + "<b>"+movieCastObject[counter].character+"</b> : "+movieCastObject[counter].name+"<br/>";
				}
			}
			movieCrewString = movieCrewString + "</p>"
		}
		document.getElementById("movieCrew").innerHTML = "<pre>" + movieCrewString + "</pre>";
	};
	xhrCrewObject.send(null);
}

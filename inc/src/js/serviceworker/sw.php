<?php


$sw="var CACHE_VERSION='app-v20';
var CACHE_FILES=[
      './',
      './index.html', 
];

/*s*/
/*self.addEventListener('install',(e)=>{
  e.waitUntil(
    caches.open('fennec_store').then((cache)=>cache.addAll(CACHE_FILES)),
  );
});

self.addEventListener('fetch',(e)=>{
  console.log(e.request.url);
  e.respondWith(
    caches.match(e.request).then((response)=>response||fetch(e.request)),
  );
});*/
/*e*/

//importScripts('./cache.js');
self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_VERSION)
            .then(function (cache) {
                console.log('Opened cache');
                return cache.addAll(CACHE_FILES);
            })
    );
});

self.addEventListener('fetch', function (event) {
    let online = navigator.onLine
    if(!online){
        event.respondWith(
            caches.match(event.request).then(function(res){
                if(res){
                    return res;
                }
                requestBackend(event);
            })
        )
    }
});

function requestBackend(event){
    var url = event.request.clone();
    return fetch(url).then(function(res){
        //if not a valid response send the error
        if(!res || res.status !== 200 || res.type !== 'basic'){
            return res;
        }

        var response = res.clone();

        caches.open(CACHE_VERSION).then(function(cache){
            cache.put(event.request, response);
        });

        return res;
    })
}

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function(keys){
            return Promise.all(keys.map(function(key, i){
                if(key !== CACHE_VERSION){
                    return caches.delete(keys[i]);
                }
            }))
        })
    )
});";

echo $sw;
?>
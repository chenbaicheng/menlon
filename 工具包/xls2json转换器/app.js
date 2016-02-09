node_xj = require("xls-to-json");
 node_xj({
  input: "20150521161716212.xls", // input xls 
  output: "output.json" // output json 
  //sheet: "sheet1", // specific sheetname 
 }, function(err, result) {
  if(err) {
   console.error(err);
  } else {
   console.log(result);
  }
 });
/*

Developed by Abhay Bisht
It uses Javascript to scan each tag's classs and appiled style to that tag according to the class specific combination

To use it in ur project just email me at abhaybishthestudent@gmail.com

*/

 function css(el, props, debug, callback)
{

    for (let i in props)
      {
		  el.style[i] = props[i];
		if(debug)
        	console.log(el.tagName +"=>"+i+":"+props[i])
      }

    if (callback)
        callback();
}

const prop1 = {
    p : "padding",
    m : "margin",
    rc : "borderRadius",
    d : "display",
    bdr : "border",
    t : "textAlign",
    w : "width",
    h : "height",
    f : "fontSize",
    bg : "background",
    clr : "color",
    out : "outline",
    gtc : "gridTemplateColumns",
    ff : "fontFamily",
    btm : "bottom",
    top : "top",
    left: "left",
    right : "right",
    ovrflw : "overflow",
    pos : "position",
    vcblt : "visibility"
}

const prop2 = {
    T : "Top",
    B : "Bottom",
    L : "Left",
    R : "Right",
    lg : "",	//lg for Linear Gradient
	rgba : "", // rgba for aplha value in text
	C : "Color"
}

const val = {
    xxs : "1px",
    xs : "3px",
    s : "5px",
    l : "15px",
    xl : "20px",
    xxl : "25px",
    xxxl : "30px",
    no : "none",
    b : "block",
    ib : "inline-block",
    flx : "flex",
    c : "center",
    lft : "left",
    r : "right",
    "100" : "100%",
    drk : "#333",
    n : "0px",
    ig : "inline-grid",
    hdn : "hidden",
    a : "auto",
    abs : "absolute",
    rel : "relative",
    vcbl : "visible"
}

const def = "10px";

const spProp = {
    "f-c-c" : {
        display : "flex", 
        flexDirection : "column",
        justifyContent : "center"
    },
    "f-r-c" : {
        display : "flex", 
        flexDirection : "row",
        justifyContent : "center"
    },
    bb : {
        boxSizing : "border-box"
    },
    bnw : {
        backgroundColor : "#333",
        color: "white"
    },
    bold : {
        fontWeight : "500"
    },
    bolder : {
        fontWeight : "900"
    },
    "bold-n" : {
        fontWeight : "100",
        color: "grey"
    },
    bdr : {
	"border" : "1px solid black"
	},
    "trns-txt" : {
		background : "url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/990140/download.png)   -20px -20px fixed",
  	  "-webkit-text-fill-color" : "transparent",
	    "-webkit-background-clip" : "text"
	},
    flxwrp : 
    {
    	"flexWrap" : "wrap"
   } 
}

function apply_CSS (cb)
{
    let body = document.body;
    let bc = body.children

    apply_style(body, body.className.split(" "))
    loop(bc)
    
    if(cb)
        cb();
}

var n=1;
var z = 0;

function loop (arr)
{
    for(let i of arr)
    {

        if(i.children.length)
        {
            let clas = i.className.split(" ");
            apply_style(i, clas);
            let cc = i.children;
            loop(cc);
        }
        else
        {
            let clas = i.className.split(" ")
            apply_style(i, clas)
        }
    }
}

function apply_style (el, clas)
{
// clas = array of classes
//console.log(el.tagName + " : (" + el.className+")")
let debug = false;
if(clas.includes("debug"))
	debug = true
if(el.className.length == 0)
	return;

let styleRule = {};
    for(let i of clas)
    {
 // i = "prop1-prop2-val";
 
    if(Object.keys(spProp).includes(i))
    {
    	//console.log("spProp used : "+i);
        css(el, spProp[i], false)
        continue;
    }
    
        let key ="", valu = "";
        let styleName = i.split("-");
        
        if(!Object.keys(prop1).includes(styleName[0]))
        	continue;
        
        for(let j of styleName)
        {
            if(Object.keys(prop1).includes(j))
            {
                key = prop1[j]
                continue;
            }
            else
            if(Object.keys(prop2).includes(j))
            {
                key += prop2[j]
                continue;
            }
            else
            if(Object.keys(val).includes(j))
            {    
				valu = val[j]
                continue;
            }
            else
            {
                valu = parse_val(styleName, j, debug);
            }
        }
    if(!valu)
    {
    	if(styleName[0] == "bdr")
        	valu = parse_val(styleName, "border case");
         else
    		valu = def;
    }
    
    styleRule[key] = valu;
    //populate the styleRule obj for css function...  
    }
    
    if(debug)
    {
       console.log("showCSS : " + JSON.stringify(styleRule))
    }
    css(el, styleRule)
}

function parse_val(sn, val, debug)//sn mean styleName var in Loop of apply_CSS function
{
	//console.log("Inside Value Parser");
	let parsed_val = null;
	let v = sn[sn.length - 1]; // real value for Parsing
	let z = (v[v.length -1])
	let fc = sn[0]			// prop1
	let sc = sn[1];		//  prop2
    let tc = sn[2]		// val, for bdr prop
    if(fc === "bg" || fc == "clr")
    {
    	if(sc == "lg")
    	{
    		//console.log(`v : ${v}`);
    		parsed_val = `linear-gradient(${v})`;
    	}
    	else
		if(sc == 'rgba')
		{
			parsed_val = `rgba(${v})`;
		}
		else
 	       parsed_val = v;
    }
    else
	if(fc == "bdr")
	{
		if(sc == "C")		// for color we use C for prop2
			parsed_val = v; // ex : bdr-C-green
		else		
		if(!Object.keys(prop2).includes(sc) || tc)
			parsed_val = `${v} solid`
		else
			parsed_val = "1px solid";
	}
    else
    if(z == "p")
    {
        parsed_val = v+"x";
    }
    else
	if(v.endsWith("px") && v.startsWith("n"))
	{
		if(debug)
			console.log("Negative Value parser");
		parsed_val = v.replace("n", "-");
	}
    else
    if(z == "%" || v.includes("vh") || v.includes("rem") || "ff,left,right,btm,top".split(",").includes(fc))
    {
        parsed_val = v
    }
    else
    if(fc == "p" || fc == "m")
    {
    	parsed_val = v.replace(",", " ");
    }
    else
        parsed_val = null;
  
  if(debug)
   	console.log(`(${val}) : ${v} -> ${parsed_val}`)
    return parsed_val;
}

function cl(txt){
   console.log(txt)
}

window.onload = () =>
{
  apply_CSS();
}







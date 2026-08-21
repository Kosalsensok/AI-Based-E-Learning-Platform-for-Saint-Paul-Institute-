import{Et as e,F as t,J as n,d as r,et as i,it as a,q as o,u as s,v as c,x as l,y as u}from"./vendor-ui-BJqjH_a2.js";import{Ct as d,Pt as f}from"./vendor-vue-Blsuj8KK.js";import{t as p}from"./StudentLayout-DXyqsTTP.js";var m={class:`space-y-6`},h={class:`bg-gradient-to-r from-cyan-950 via-slate-900 to-indigo-950 border border-cyan-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4`},g={class:`flex items-center gap-2`},_=[`disabled`],v={class:`grid grid-cols-1 lg:grid-cols-2 gap-6`},y={class:`bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl flex flex-col h-[520px]`},b={class:`bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl flex flex-col h-[520px]`},x={class:`px-4 py-3 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between`},S={class:`flex-1 p-4 font-mono text-xs text-slate-200 overflow-y-auto custom-scrollbar whitespace-pre-wrap leading-relaxed`},C=l({__name:`ItLab`,setup(l){let C=i(`#include <stdio.h>
#include <stdlib.h>

// Saint Paul Institute - C Coding Lab
int main() {
    int scores[] = {85, 92, 78, 95, 88};
    int n = sizeof(scores) / sizeof(scores[0]);
    int sum = 0;
    
    printf("=== SPI Student Score Calculator ===\\n");
    for(int i = 0; i < n; i++) {
        sum += scores[i];
        printf("Score #%d: %d\\n", i + 1, scores[i]);
    }
    
    double avg = (double)sum / n;
    printf("------------------------------------\\n");
    printf("Total Sum: %d\\n", sum);
    printf("Average Score: %.2f\\n", avg);
    
    return 0;
}`),w=i(!1),T=i(`=== SPI Student Score Calculator ===
Score #1: 85
Score #2: 92
Score #3: 78
Score #4: 95
Score #5: 88
------------------------------------
Total Sum: 438
Average Score: 87.60

[Program finished with exit code 0 in 0.042s]`),E=()=>{w.value=!0,T.value=`Compiling with gcc (SPI C11 Runner)...`,setTimeout(()=>{w.value=!1,T.value=`=== SPI Student Score Calculator ===
Score #1: 85
Score #2: 92
Score #3: 78
Score #4: 95
Score #5: 88
------------------------------------
Total Sum: 438
Average Score: 87.60

[Program finished with exit code 0 in 0.038s]`},500)};return(i,l)=>(t(),r(p,{title:`Practice Lab — IT & Networking Coding Lab`},{default:o(()=>[s(`div`,m,[s(`div`,h,[l[4]||(l[4]=s(`div`,null,[s(`span`,{class:`px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold uppercase tracking-wider`},` 💻 Department of Information Technology & Networking `),s(`h1`,{class:`text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2`},[s(`span`,null,`INTERACTIVE CODING LAB & TERMINAL`)]),s(`p`,{class:`text-xs text-slate-300 mt-1`},` Online GCC C/C++ compiler and Linux terminal sandbox environment for hands-on programming exercises `)],-1)),s(`div`,g,[u(a(d),{href:`/student/practice-lab`,class:`px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors`},{default:o(()=>[...l[2]||(l[2]=[c(` All Major Labs `,-1)])]),_:1}),s(`button`,{onClick:E,disabled:w.value,class:`px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5`},[...l[3]||(l[3]=[s(`span`,null,`▶ Run Code (F5)`,-1)])],8,_)])]),s(`div`,v,[s(`div`,y,[l[5]||(l[5]=s(`div`,{class:`px-4 py-3 bg-slate-950 border-b border-slate-800 flex items-center justify-between`},[s(`div`,{class:`flex items-center gap-2`},[s(`span`,{class:`w-3 h-3 rounded-full bg-red-500/80`}),s(`span`,{class:`w-3 h-3 rounded-full bg-amber-500/80`}),s(`span`,{class:`w-3 h-3 rounded-full bg-emerald-500/80`}),s(`span`,{class:`ml-2 text-xs font-mono text-slate-400`},`main.c (C11 Compiler)`)]),s(`span`,{class:`text-[10px] text-cyan-400 font-mono`},`GCC 13.2`)],-1)),n(s(`textarea`,{"onUpdate:modelValue":l[0]||(l[0]=e=>C.value=e),class:`flex-1 w-full bg-slate-900 text-emerald-300 font-mono text-xs p-4 leading-relaxed resize-none focus:outline-none custom-scrollbar`,spellcheck:`false`},null,512),[[f,C.value]])]),s(`div`,b,[s(`div`,x,[l[6]||(l[6]=s(`div`,{class:`flex items-center gap-2`},[s(`span`,{class:`text-xs font-mono text-slate-300`},`📟 Terminal Output (stdout)`)],-1)),s(`button`,{onClick:l[1]||(l[1]=e=>T.value=``),class:`text-[11px] text-slate-400 hover:text-slate-200`},` Clear Console `)]),s(`div`,S,e(T.value),1)])])])]),_:1}))}});export{C as default};
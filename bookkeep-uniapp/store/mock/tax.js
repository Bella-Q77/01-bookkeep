export const regionTaxData = [
  {
    name: '中国大陆',
    rates: [
      { min: 0, max: 36000, rate: 3, quickDeduction: 0, range: "不超过36,000元" },
      { min: 36000, max: 144000, rate: 10, quickDeduction: 2520, range: "36,000-144,000元" },
      { min: 144000, max: 300000, rate: 20, quickDeduction: 16920, range: "144,000-300,000元" },
      { min: 300000, max: 420000, rate: 25, quickDeduction: 31920, range: "300,000-420,000元" },
      { min: 420000, max: 660000, rate: 30, quickDeduction: 52920, range: "420,000-660,000元" },
      { min: 660000, max: 960000, rate: 35, quickDeduction: 85920, range: "660,000-960,000元" },
      { min: 960000, max: -1, rate: 45, quickDeduction: 181920, range: "超过960,000元" }
    ]
  },
  {
    name: '香港',
    rates: [
      { min: 0, max: 50000, rate: 2, quickDeduction: 0, range: "不超过50,000港币" },
      { min: 50000, max: 100000, rate: 6, quickDeduction: 2000, range: "50,000-100,000港币" },
      { min: 100000, max: 150000, rate: 10, quickDeduction: 6000, range: "100,000-150,000港币" },
      { min: 150000, max: 200000, rate: 14, quickDeduction: 12000, range: "150,000-200,000港币" },
      { min: 200000, max: -1, rate: 17, quickDeduction: 18000, range: "超过200,000港币" }
    ]
  },
  {
    name: '美国',
    rates: [
      { min: 0, max: 9875, rate: 10, quickDeduction: 0, range: "$0-$9,875" },
      { min: 9875, max: 40125, rate: 12, quickDeduction: 987.5, range: "$9,875-$40,125" },
      { min: 40125, max: 85525, rate: 22, quickDeduction: 4617.5, range: "$40,125-$85,525" },
      { min: 85525, max: 163300, rate: 24, quickDeduction: 14605.5, range: "$85,525-$163,300" },
      { min: 163300, max: 207350, rate: 32, quickDeduction: 33271.5, range: "$163,300-$207,350" },
      { min: 207350, max: 518400, rate: 35, quickDeduction: 47367.5, range: "$207,350-$518,400" },
      { min: 518400, max: -1, rate: 37, quickDeduction: 156235, range: "超过$518,400" }
    ]
  }
];